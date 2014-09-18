<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\AreaFacade;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ModelBundle\Document\Area;
use PHPOrchestra\ModelBundle\Document\Node;
use PHPOrchestra\ModelBundle\Model\AreaInterface;
use PHPOrchestra\ModelBundle\Model\NodeInterface;
use PHPOrchestra\ModelBundle\Model\TemplateInterface;
use PHPOrchestra\ModelBundle\Repository\NodeRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AreaTransformer
 */
class AreaTransformer extends AbstractTransformer
{
    protected $nodeRepository;

    /**
     * @param NodeRepository $nodeRepository
     */
    public function __construct(NodeRepository $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @param AreaInterface $mixed
     * @param NodeInterface $node
     *
     * @return FacadeInterface
     */
    public function transform($mixed, NodeInterface $node = null)
    {
        $facade = new AreaFacade();

        $facade->areaId = $mixed->getAreaId();
        $facade->classes = implode(',', $mixed->getClasses());
        foreach ($mixed->getAreas() as $subArea) {
            $facade->addArea($this->getTransformer('area')->transform($subArea, $node));
        }
        foreach ($mixed->getBlocks() as $blockPosition => $block) {
            if (0 === $block['nodeId']) {
                $facade->addBlock($this->getTransformer('block')->transform(
                    $node->getBlocks()->get($block['blockId']),
                    true,
                    $node->getNodeId(),
                    $block['blockId'],
                    $mixed->getAreaId(),
                    $blockPosition
                ));
            } else {
                $otherNode = $this->nodeRepository->findOneByNodeId($block['nodeId']);
                $facade->addBlock($this->getTransformer('block')->transform(
                    $otherNode->getBlocks()->get($block['blockId']),
                    false,
                    $otherNode->getNodeId(),
                    $block['blockId'],
                    $mixed->getAreaId(),
                    $blockPosition
                ));
            }
        }
        $facade->boDirection = $mixed->getBoDirection();
        $facade->uiModel = $this->getTransformer('ui_model')->transform(array('label' => $mixed->getAreaId()));
        $facade->addLink('_self_form', $this->getRouter()->generate('php_orchestra_backoffice_area_form',
            array(
                'nodeId' => $node->getNodeId(),
                'areaId' => $mixed->getAreaId(),
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        ));

        return $facade;
    }

    /**
     * @param AreaInterface     $mixed
     * @param TemplateInterface $template
     *
     * @return FacadeInterface
     */
    public function transformFromTemplate($mixed, TemplateInterface $template = null)
    {
        $facade = new AreaFacade();

        $facade->areaId = $mixed->getAreaId();
        $facade->classes = implode(',', $mixed->getClasses());
        foreach ($mixed->getAreas() as $subArea) {
            $facade->addArea($this->getTransformer('area')->transformFromTemplate($subArea, $template));
        }
        $facade->boDirection = $mixed->getBoDirection();
        $facade->uiModel = $this->getTransformer('ui_model')->transform(array('label' => $mixed->getAreaId()));
        $facade->addLink('_self_form', $this->getRouter()->generate('php_orchestra_backoffice_template_area_form',
            array(
                'templateId' => $template->getTemplateId(),
                'areaId' => $mixed->getAreaId(),
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'area';
    }
}
