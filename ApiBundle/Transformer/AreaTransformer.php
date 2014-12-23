<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Exceptions\HttpException\AreaTransformerHttpException;
use PHPOrchestra\ApiBundle\Facade\AreaFacade;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\BackofficeBundle\Manager\AreaManager;
use PHPOrchestra\ModelInterface\Model\AreaInterface;
use PHPOrchestra\ModelInterface\Model\NodeInterface;
use PHPOrchestra\ModelInterface\Model\TemplateInterface;
use PHPOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class AreaTransformer
 */
class AreaTransformer extends AbstractTransformer
{
    protected $nodeRepository;
    protected $areaManager;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     * @param AreaManager             $areaManager
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, AreaManager $areaManager)
    {
        $this->nodeRepository = $nodeRepository;
        $this->areaManager = $areaManager;
    }

    /**
     * @param AreaInterface $mixed
     * @param NodeInterface $node
     * @param string        $parentAreaId
     *
     * @return FacadeInterface
     * @throws AreaTransformerHttpException
     */
    public function transform($mixed, NodeInterface $node = null, $parentAreaId = null)
    {
        $facade = new AreaFacade();

        if (!$node instanceof NodeInterface) {
            throw new AreaTransformerHttpException();
        }

        $facade->label = $mixed->getLabel();
        $facade->areaId = $mixed->getAreaId();
        $facade->classes = implode(',', $mixed->getClasses());
        foreach ($mixed->getAreas() as $subArea) {
            $facade->addArea($this->getTransformer('area')->transform($subArea, $node, $mixed->getAreaId()));
        }
        foreach ($mixed->getBlocks() as $blockPosition => $block) {
            $otherNode = $node;
            $isInside = true;
            if (0 !== $block['nodeId'] && $node->getNodeId() != $block['nodeId']) {
                $otherNode = $this->nodeRepository->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($block['nodeId'], $node->getLanguage());
                $isInside = false;
            }
            $facade->addBlock($this->getTransformer('block')->transform(
                $otherNode->getBlock($block['blockId']),
                $isInside,
                $otherNode->getNodeId(),
                $block['blockId'],
                $mixed->getAreaId(),
                $blockPosition,
                $otherNode->getId()
            ));
        }
        $facade->boDirection = $mixed->getBoDirection();

        $facade->uiModel = $this->getTransformer('ui_model')->transform(
            array(
                'label' => $mixed->getLabel(),
                'class' => $mixed->getHtmlClass(),
                'id' => $mixed->getAreaId()
            )
        );
        $facade->addLink('_self_form', $this->generateRoute('php_orchestra_backoffice_area_form', array(
            'nodeId' => $node->getId(),
            'areaId' => $mixed->getAreaId(),
        )));
        $facade->addLink('_self_block', $this->generateRoute('php_orchestra_api_area_update_block', array(
            'nodeId' => $node->getId(),
            'areaId' => $mixed->getAreaId()
        )));
        $facade->addLink('_self', $this->generateRoute('php_orchestra_api_area_show_in_node', array(
            'nodeId' => $node->getId(),
            'areaId' => $mixed->getAreaId()
        )));

        if ($parentAreaId) {
            $facade->addLink('_self_delete', $this->generateRoute('php_orchestra_api_area_delete_in_node_area',
                array(
                    'nodeId' => $node->getId(),
                    'parentAreaId' => $parentAreaId,
                    'areaId' => $mixed->getAreaId()
                )
            ));

        } else {
            $facade->addLink('_self_delete', $this->generateRoute('php_orchestra_api_area_delete_in_node',
                array(
                    'nodeId' => $node->getId(),
                    'areaId' => $mixed->getAreaId(),
                )
            ));
        }

        return $facade;
    }

    /**
     * @param AreaInterface          $mixed
     * @param TemplateInterface|null $template
     * @param string|null            $parentAreaId
     *
     * @return FacadeInterface
     */
    public function transformFromTemplate($mixed, TemplateInterface $template = null, $parentAreaId = null)
    {
        $facade = new AreaFacade();

        $templateId = null;
        if ($template instanceof TemplateInterface) {
            $templateId = $template->getTemplateId();
        }

        $facade->label = $mixed->getLabel();
        $facade->areaId = $mixed->getAreaId();
        $facade->classes = implode(',', $mixed->getClasses());
        foreach ($mixed->getAreas() as $subArea) {
            $facade->addArea($this->getTransformer('area')->transformFromTemplate($subArea, $template, $mixed->getAreaId()));
        }

        $facade->boDirection = $mixed->getBoDirection();

        $facade->uiModel = $this->getTransformer('ui_model')->transform(
            array(
                'label' => $mixed->getLabel(),
                'class' => $mixed->getHtmlClass(),
                'id' => $mixed->getAreaId()
            )
        );
        $facade->addLink('_self_form', $this->generateRoute('php_orchestra_backoffice_template_area_form',
            array(
                'templateId' => $templateId,
                'areaId' => $mixed->getAreaId(),
            )
        ));

        $facade->addLink('_self', $this->generateRoute('php_orchestra_api_area_show_in_template', array(
            'templateId' => $templateId,
            'areaId' => $mixed->getAreaId()
        )));

        if ($parentAreaId) {
            $facade->addLink('_self_delete', $this->generateRoute('php_orchestra_api_area_delete_in_template_area',
                array(
                    'templateId' => $templateId,
                    'parentAreaId' => $parentAreaId,
                    'areaId' => $mixed->getAreaId()
                )
            ));

        } else {
            $facade->addLink('_self_delete', $this->generateRoute('php_orchestra_api_area_delete_in_template',
                array(
                    'templateId' => $templateId,
                    'areaId' => $mixed->getAreaId(),
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
                $blockNode = $this->nodeRepository->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($blockArray['nodeId'], $node->getLanguage());
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
