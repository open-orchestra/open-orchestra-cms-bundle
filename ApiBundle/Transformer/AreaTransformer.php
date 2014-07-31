<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\AreaFacade;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ModelBundle\Document\Area;
use PHPOrchestra\ModelBundle\Document\Node;
use PHPOrchestra\ModelBundle\Model\AreaInterface;
use PHPOrchestra\ModelBundle\Model\NodeInterface;
use PHPOrchestra\ModelBundle\Repository\NodeRepository;

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
        foreach ($mixed->getSubAreas() as $subArea) {
            $facade->addArea($this->getTransformer('area')->transform($subArea, $node));
        }
        foreach ($mixed->getBlocks() as $block) {
            if (0 == $block['nodeId']) {
                $facade->addBlock($this->getTransformer('block')->transform($node->getBlocks()->get($block['blockId'])));
            } else {
                $node = $this->nodeRepository->findOneByNodeId($block['nodeId']);
                $facade->addBlock($this->getTransformer('block')->transform(
                    $node->getBlocks()->get($block['blockId']),
                    false
                ));
            }
        }
        $facade->boDirection = $mixed->getBoDirection();
        $facade->uiModel = $this->getTransformer('ui_model')->transform(array('label' => $mixed->getAreaId()));

        return $facade;
    }

    /**
     * @param FacadeInterface|AreaFacade $facade
     * @param Area|null                  $source
     * @param Node|null                  $node
     *
     * @return mixed|void
     */
    public function reverseTransform(FacadeInterface $facade, $source = null, $node = null)
    {
        if (null === $source) {
            $source = new Area();
        }

        if (isset($facade->areaId)) {
            $source->setAreaId($facade->areaId);
        }

        if (isset($facade->classes) && '' != $facade->classes) {
            $source->setClasses(explode(',', $facade->classes));
        }

        foreach ($facade->getAreas() as $area) {
            $source->addSubArea($this->getTransformer('area')->reverseTransform($area, null, $node));
        }

        foreach ($facade->getBlocks() as $block) {
            $blockArray = $this->getTransformer('block')->reverseTransform($block, $node);
            if (array_key_exists('block', $blockArray)) {
                $node->setBlock($blockArray['blockId'], $blockArray['block']);
                unset($blockArray['block']);
            }
            $source->addBlock($blockArray);
        }

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
