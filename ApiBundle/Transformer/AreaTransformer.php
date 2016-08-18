<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\AreaTransformerHttpException;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TransverseNodePanelStrategy;
use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\Backoffice\Manager\AreaManager;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\TemplateInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplatePanelStrategy;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use UnexpectedValueException;
use OpenOrchestra\Backoffice\Manager\NodeManager;

/**
 * Class AreaTransformer
 */
class AreaTransformer extends AbstractSecurityCheckerAwareTransformer implements TransformerWithTemplateContextInterface
{
    protected $nodeRepository;
    protected $areaManager;
    protected $nodeManager;

    /**
     * @param string                        $facadeClass
     * @param NodeRepositoryInterface       $nodeRepository
     * @param AreaManager                   $areaManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param NodeManager                   $nodeManager
     */
    public function __construct(
        $facadeClass,
        NodeRepositoryInterface $nodeRepository,
        AreaManager $areaManager,
        AuthorizationCheckerInterface $authorizationChecker,
        NodeManager $nodeManager
    ){
        parent::__construct($facadeClass, $authorizationChecker);
        $this->nodeRepository = $nodeRepository;
        $this->areaManager = $areaManager;
        $this->nodeManager = $nodeManager;
    }

    /**
     * @param AreaInterface      $area
     * @param NodeInterface|null $node
     * @param string|null        $parentAreaId
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     * @throws AreaTransformerHttpException
     */
    public function transform($area, NodeInterface $node = null, $parentAreaId = null)
    {
        $facade = $this->newFacade();

        if (!$area instanceof AreaInterface) {
            throw new TransformerParameterTypeException();
        }

        if (!$node instanceof NodeInterface) {
            throw new AreaTransformerHttpException();
        }

        $facade->label = $area->getLabel();
        $facade->areaId = $area->getAreaId();
        $facade->areaType = $area->getAreaType();
        $facade->width = $area->getWidth();
        $facade->label = $area->getLabel();
        $facade->classes = $area->getHtmlClass();
        $facade->uiModel = $this->getTransformer('ui_model')->transform(
            array(
                'label' => $area->getLabel(),
                'class' => $area->getHtmlClass(),
                'id' => $area->getAreaId()
            )
        );
        $facade->editable = $this->authorizationChecker->isGranted($this->getEditionNodeRole($node), $node);

        foreach ($area->getAreas() as $subArea) {
            $facade->addArea($this->transform($subArea, $node, $area->getAreaId()));
        }

        foreach ($area->getBlocks() as $blockPosition => $block) {
            $otherNode = $node;
            $isInside = true;
            if (0 !== $block['nodeId'] && $node->getNodeId() != $block['nodeId']) {
                $otherNode = $this->nodeRepository->findInLastVersion($block['nodeId'], $node->getLanguage(), $node->getSiteId());
                $isInside = false;
            }
            $facade->addBlock($this->getTransformer('block')->transform(
                $otherNode->getBlock($block['blockId']),
                $isInside,
                $otherNode->getNodeId(),
                $block['blockId'],
                $area->getAreaId(),
                $blockPosition,
                $otherNode->getId()
            ));
        }

        if ($facade->editable) {
            $this->addLinksFromNode($facade, $node, $area, $parentAreaId);
        }

        return $facade;
    }

    /**
     * @param AreaInterface     $area
     * @param TemplateInterface $template
     * @param string|null           $parentAreaId
     *
     * @return FacadeInterface
     */
    public function transformFromTemplate(AreaInterface $area, TemplateInterface $template, $parentAreaId = null)
    {
        $facade = $this->newFacade();
        $facade->label = $area->getLabel();
        $facade->areaId = $area->getAreaId();
        $facade->areaType = $area->getAreaType();
        $facade->width = $area->getWidth();
        $facade->classes = $area->getHtmlClass();
        $facade->label = $area->getLabel();
        $facade->editable = $this->authorizationChecker->isGranted(TreeTemplatePanelStrategy::ROLE_ACCESS_UPDATE_TEMPLATE, $template);

        $facade->uiModel = $this->getTransformer('ui_model')->transform(
            array(
                'label' => $area->getLabel(),
                'class' => $area->getHtmlClass(),
                'id' => $area->getAreaId()
            )
        );

        foreach ($area->getAreas() as $subArea) {
            $facade->addArea($this->transformFromTemplate($subArea, $template, $area->getAreaId()));
        }

        if ($facade->editable) {
            $this->addLinksFromTemplate($facade, $template, $area, $parentAreaId);
        }

        return $facade;
    }

    /**
     * @param FacadeInterface    $facade
     * @param AreaInterface|null $source
     * @param NodeInterface|null $node
     *
     * @return AreaInterface
     *
     * @throws UnexpectedValueException
     */
    public function reverseTransform(FacadeInterface $facade, $source = null, NodeInterface $node = null)
    {
        if (!$source instanceof AreaInterface) {
            throw new UnexpectedValueException("source must be an instance of AreaInterface");
        }
        $subAreaFacade = $facade->getAreas();
        $newOrderSubAreas = array();
        /** @var AreaInterface $subArea */
        foreach ($source->getAreas() as $subArea) {
            $order = $this->getAreaOrderInChildren($subArea->getAreaId(), $subAreaFacade);
            $newOrderSubAreas[$order] = $subArea;
        }
        ksort($newOrderSubAreas);
        $source->setAreas(new ArrayCollection($newOrderSubAreas));

        if ($node instanceof NodeInterface) {
            $blocks = $facade->getBlocks();
            $blockDocument = array();
            foreach ($blocks as $position => $blockFacade) {
                $blockDocument[$position] = $this->reverseTransformBlockInNode($source, $blockFacade, $node);
            }

            $this->areaManager
                ->deleteAreaFromBlock($source->getBlocks(), $blockDocument, $source->getAreaId(), $node);
            $source->setBlocks($blockDocument);
            $this->nodeManager->removeUnusedBlocks($node);
        }

        return $source;
    }

    /**
     * @param string $areaId
     * @param array  $areasChildren
     *
     * @return int
     */
    protected function getAreaOrderInChildren($areaId, array $areasChildren)
    {
        foreach ($areasChildren as $areaFacade) {
            if ($areaId === $areaFacade->areaId)
                return $areaFacade->order;
        }

        return 0;
    }

    /**
     * @param AreaInterface   $areaSource
     * @param FacadeInterface $blockFacade
     * @param NodeInterface   $node
     *
     * @return array
     */
    protected function reverseTransformBlockInNode(
        AreaInterface $areaSource,
        FacadeInterface $blockFacade,
        NodeInterface $node
    ) {
        $blockArray = $this->getTransformer('block')->reverseTransformToArray($blockFacade, $node);
        $block = $node->getBlock($blockArray['blockId']);
        $nodeId = 0;
        if ($blockArray['nodeId'] !== 0) {
            $nodeTransverse = $this->nodeRepository
                ->findInLastVersion($blockArray['nodeId'], $node->getLanguage(), $node->getSiteId());
            $block = $nodeTransverse->getBlock($blockArray['blockId']);
            $nodeId = $node->getId();
        }
        $block->addArea(array('nodeId' => $nodeId, 'areaId' => $areaSource->getAreaId()));

        return $blockArray;
    }

    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    protected function getEditionNodeRole(NodeInterface $node)
    {
        if (NodeInterface::TYPE_TRANSVERSE === $node->getNodeType()) {
            return TransverseNodePanelStrategy::ROLE_ACCESS_UPDATE_GENERAL_NODE;
        } elseif (NodeInterface::TYPE_ERROR === $node->getNodeType()) {
            return TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_ERROR_NODE;
        }

        return TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE;
    }

    /**
     * @param FacadeInterface $facade
     * @param NodeInterface   $node
     * @param AreaInterface   $area
     * @param string          $parentAreaId
     */
    protected function addLinksFromNode(
        FacadeInterface $facade,
        NodeInterface $node,
        AreaInterface $area,
        $parentAreaId
    ) {
        $facade->addLink('_self_form_new_row', $this->generateRoute('open_orchestra_backoffice_node_new_row_area', array(
            'nodeId' => $node->getNodeId(),
            'language' => $node->getLanguage(),
            'version' => $node->getVersion(),
            'siteId' => $node->getSiteId(),
            'areaParentId' => $area->getAreaId(),
        )));
        $facade->addLink('_self_move_area', $this->generateRoute('open_orchestra_api_area_move_in_node', array(
            'areaParentId' => $area->getAreaId(),
            'nodeId' => $node->getNodeId(),
            'language' => $node->getLanguage(),
            'version' => $node->getVersion(),
            'siteId' => $node->getSiteId(),
        )));

        if (AreaInterface::TYPE_COLUMN === $area->getAreaType()) {
            $facade->addLink('_self_form_column', $this->generateRoute('open_orchestra_backoffice_node_area_form_column', array(
                'nodeId' => $node->getNodeId(),
                'language' => $node->getLanguage(),
                'version' => $node->getVersion(),
                'siteId' => $node->getSiteId(),
                'areaId' => $area->getAreaId(),
            )));

            $facade->addLink('_self_form_row', $this->generateRoute('open_orchestra_backoffice_node_area_form_row', array(
                'nodeId' => $node->getNodeId(),
                'language' => $node->getLanguage(),
                'version' => $node->getVersion(),
                'siteId' => $node->getSiteId(),
                'areaId' => $parentAreaId,
            )));

            $facade->addLink('_self_delete_column', $this->generateRoute('open_orchestra_api_area_column_delete_in_node', array(
                'nodeId' => $node->getNodeId(),
                'language' => $node->getLanguage(),
                'version' => $node->getVersion(),
                'siteId' => $node->getSiteId(),
                'areaId' => $area->getAreaId(),
                'areaParentId' => $parentAreaId,
            )));

            $facade->addLink('_self_delete_row', $this->generateRoute('open_orchestra_api_area_row_delete_in_node', array(
                'nodeId' => $node->getNodeId(),
                'language' => $node->getLanguage(),
                'version' => $node->getVersion(),
                'siteId' => $node->getSiteId(),
                'areaId' => $parentAreaId,
            )));

            $routeName = 'open_orchestra_api_block_list_without_transverse';
            if (NodeInterface::TYPE_TRANSVERSE !== $node->getNodeType()) {
                $routeName = 'open_orchestra_api_block_list_with_transverse';
            }
            $facade->addLink('_block_list', $this->generateRoute($routeName, array('language' => $node->getLanguage())));

            $facade->addLink('_self_update_block', $this->generateRoute('open_orchestra_api_area_update_block', array(
                'nodeId' => $node->getNodeId(),
                'language' => $node->getLanguage(),
                'version' => $node->getVersion(),
                'siteId' => $node->getSiteId(),
            )));

            $facade->addLink('_self_move_block', $this->generateRoute('open_orchestra_api_area_move_block', array(
                'nodeId' => $node->getNodeId(),
                'language' => $node->getLanguage(),
                'version' => $node->getVersion(),
                'siteId' => $node->getSiteId(),
            )));

            $facade->addLink('_self', $this->generateRoute('open_orchestra_api_area_show_in_node', array(
                'areaId' => $area->getAreaId(),
                'nodeId' => $node->getNodeId(),
                'language' => $node->getLanguage(),
                'version' => $node->getVersion(),
                'siteId' => $node->getSiteId(),
                'areaParentId' => $parentAreaId
            )));
        }
    }

    /**
     * @param FacadeInterface   $facade
     * @param TemplateInterface $template
     * @param AreaInterface     $area
     * @param string            $parentAreaId
     */
    protected function addLinksFromTemplate(
        FacadeInterface $facade,
        TemplateInterface $template,
        AreaInterface $area,
        $parentAreaId
    ) {
        $facade->addLink('_self_form_new_row', $this->generateRoute('open_orchestra_backoffice_template_new_row_area', array(
            'templateId' => $template->getTemplateId(),
            'areaParentId' => $area->getAreaId(),
        )));
        $facade->addLink('_self_move_area', $this->generateRoute('open_orchestra_api_area_move_in_template', array(
            'areaParentId' => $area->getAreaId(),
            'templateId' => $template->getTemplateId(),
        )));

        if (AreaInterface::TYPE_COLUMN === $area->getAreaType()) {
            $facade->addLink('_self_form_column', $this->generateRoute('open_orchestra_backoffice_area_form_column', array(
                'templateId' => $template->getTemplateId(),
                'areaId' => $area->getAreaId(),
            )));

            $facade->addLink('_self_form_row', $this->generateRoute('open_orchestra_backoffice_area_form_row', array(
                'templateId' => $template->getTemplateId(),
                'areaId' => $parentAreaId,
            )));

            $facade->addLink('_self_delete_column', $this->generateRoute('open_orchestra_api_area_column_delete_in_template', array(
                'templateId' => $template->getTemplateId(),
                'areaId' => $area->getAreaId(),
                'areaParentId' => $parentAreaId,
            )));

            $facade->addLink('_self_delete_row', $this->generateRoute('open_orchestra_api_area_row_delete_in_template', array(
                'templateId' => $template->getTemplateId(),
                'areaId' => $parentAreaId,
            )));
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'area';
    }
}
