<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\AreaTransformerHttpException;
use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\ApiBundle\Facade\AreaFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\BackofficeBundle\Manager\AreaManager;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\TemplateInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class AreaTransformer
 */
class AreaTransformer extends AbstractTransformer
{
    protected $nodeRepository;
    protected $areaManager;
    protected $currentSiteManager;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     * @param AreaManager             $areaManager
     * @param CurrentSiteIdInterface  $currentSiteManager
     */
    public function __construct(
        NodeRepositoryInterface $nodeRepository,
        AreaManager $areaManager,
        CurrentSiteIdInterface $currentSiteManager
    )
    {
        $this->nodeRepository = $nodeRepository;
        $this->areaManager = $areaManager;
        $this->currentSiteManager = $currentSiteManager;
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
        $facade = new AreaFacade();

        if (!$area instanceof AreaInterface) {
            throw new TransformerParameterTypeException();
        }

        if (!$node instanceof NodeInterface) {
            throw new AreaTransformerHttpException();
        }

        $facade->label = $area->getLabel();
        $facade->areaId = $area->getAreaId();
        $facade->classes = $area->getHtmlClass();
        foreach ($area->getAreas() as $subArea) {
            $facade->addArea($this->getTransformer('area')->transform($subArea, $node, $area->getAreaId()));
        }
        foreach ($area->getBlocks() as $blockPosition => $block) {
            $otherNode = $node;
            $isInside = true;
            if (0 !== $block['nodeId'] && $node->getNodeId() != $block['nodeId']) {
                $siteId = $this->currentSiteManager->getCurrentSiteId();
                $otherNode = $this->nodeRepository->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($block['nodeId'], $node->getLanguage(), $siteId);
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
        $facade->boDirection = $area->getBoDirection();

        $facade->uiModel = $this->getTransformer('ui_model')->transform(
            array(
                'label' => $area->getLabel(),
                'class' => $area->getHtmlClass(),
                'id' => $area->getAreaId()
            )
        );
        $facade->addLink('_self_form', $this->generateRoute('open_orchestra_backoffice_area_form', array(
            'nodeId' => $node->getId(),
            'areaId' => $area->getAreaId(),
        )));
        $facade->addLink('_self_block', $this->generateRoute('open_orchestra_api_area_update_block', array(
            'nodeId' => $node->getId(),
            'areaId' => $area->getAreaId()
        )));
        $facade->addLink('_self', $this->generateRoute('open_orchestra_api_area_show_in_node', array(
            'nodeId' => $node->getId(),
            'areaId' => $area->getAreaId()
        )));

        if ($parentAreaId) {
            $facade->addLink('_self_delete', $this->generateRoute('open_orchestra_api_area_delete_in_node_area',
                array(
                    'nodeId' => $node->getId(),
                    'parentAreaId' => $parentAreaId,
                    'areaId' => $area->getAreaId()
                )
            ));

        } else {
            $facade->addLink('_self_delete', $this->generateRoute('open_orchestra_api_area_delete_in_node',
                array(
                    'nodeId' => $node->getId(),
                    'areaId' => $area->getAreaId(),
                )
            ));
        }

        return $facade;
    }

    /**
     * @param AreaInterface          $area
     * @param TemplateInterface|null $template
     * @param string|null            $parentAreaId
     *
     * @return FacadeInterface
     */
    public function transformFromTemplate($area, TemplateInterface $template = null, $parentAreaId = null)
    {
        $facade = new AreaFacade();

        $templateId = null;
        if ($template instanceof TemplateInterface) {
            $templateId = $template->getTemplateId();
        }

        $facade->label = $area->getLabel();
        $facade->areaId = $area->getAreaId();
        $facade->classes = $area->getHtmlClass();
        foreach ($area->getAreas() as $subArea) {
            $facade->addArea($this->getTransformer('area')->transformFromTemplate($subArea, $template, $area->getAreaId()));
        }

        $facade->boDirection = $area->getBoDirection();
        $facade->x = $area->getX();
        $facade->y = $area->getY();
        $facade->width = $area->getWidth();
        $facade->height = $area->getHeight();

        $facade->uiModel = $this->getTransformer('ui_model')->transform(
            array(
                'label' => $area->getLabel(),
                'class' => $area->getHtmlClass(),
                'id' => $area->getAreaId()
            )
        );
        $facade->addLink('_self_form', $this->generateRoute('open_orchestra_backoffice_template_area_form',
            array(
                'templateId' => $templateId,
                'areaId' => $area->getAreaId(),
            )
        ));

        $facade->addLink('_self', $this->generateRoute('open_orchestra_api_area_show_in_template', array(
            'templateId' => $templateId,
            'areaId' => $area->getAreaId()
        )));

        if ($parentAreaId) {
            $facade->addLink('_self_delete', $this->generateRoute('open_orchestra_api_area_delete_in_template_area',
                array(
                    'templateId' => $templateId,
                    'parentAreaId' => $parentAreaId,
                    'areaId' => $area->getAreaId()
                )
            ));

        } else {
            $facade->addLink('_self_delete', $this->generateRoute('open_orchestra_api_area_delete_in_template',
                array(
                    'templateId' => $templateId,
                    'areaId' => $area->getAreaId(),
                )
            ));
            $facade->addLink('_self_update', $this->generateRoute('open_orchestra_api_area_update_in_template',
                array(
                    'templateId' => $templateId,
                    'areaId' => $area->getAreaId(),
                )
            ));
        }

        return $facade;
    }

    /**
     * @param AreaFacade|FacadeInterface $facade
     * @param AreaInterface|null         $source
     * @param NodeInterface|null         $node
     *
     * @return mixed|AreaInterface
     */
    public function reverseTransform(FacadeInterface $facade, $source = null, NodeInterface $node = null)
    {
        $blocks = $facade->getBlocks();
        $blockDocument = array();

        foreach ($blocks as $position => $blockFacade) {
            $blockArray = $this->getTransformer('block')->reverseTransformToArray($blockFacade, $node);
            $blockDocument[$position] = $blockArray;
            $block = $node->getBlock($blockArray['blockId']);
            if ($blockArray['nodeId'] !== 0) {
                $siteId = $this->currentSiteManager->getCurrentSiteId();
                $blockNode = $this->nodeRepository->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($blockArray['nodeId'], $node->getLanguage(), $siteId);
                $block = $blockNode->getBlock($blockArray['blockId']);
            }
            $block->addArea(array('nodeId' => $node->getId(), 'areaId' => $source->getAreaId()));
        }

        $this->areaManager->deleteAreaFromBlock($source->getBlocks(), $blockDocument, $source->getAreaId(), $node);
        $source->setBlocks($blockDocument);

        return $source;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'area';
    }
}
