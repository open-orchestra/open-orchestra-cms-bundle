<?php

namespace PHPOrchestra\BackofficeBundle\Manager;

use PHPOrchestra\ModelBundle\Document\Area;
use PHPOrchestra\ModelBundle\Model\AreaInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use PHPOrchestra\ModelBundle\Model\NodeInterface;
use PHPOrchestra\ModelBundle\Repository\NodeRepository;

/**
 * Class BlockManager
 */
class BlockManager
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
     * @param NodeInterface $node
     *
     * @return bool
     */
    public function blockConsistency($node)
    {
        foreach ($node->getBlocks() as $block) {
            if (!$this->checkAreaRef($block->getAreas(), $node, $block)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array          $refAreas
     * @param NodeInterface  $nodeRef
     * @param BlockInterface $block
     *
     * @return bool
     */
    protected function checkAreaRef($refAreas, $nodeRef, $block)
    {
        foreach ($refAreas as $refArea) {
            if ($refArea['nodeId'] === $nodeRef->getId() || $refArea['nodeId'] === 0) {
                $node = $nodeRef;
            } else {
                $otherNode = $this->nodeRepository->find($refArea['nodeId']);
                $node = $otherNode;
            }
            $result = $this->AreaIdExist($refArea['areaId'], $node->getAreas());

            if (null === $result) {
                return false;
            } else {
                if (!$this->checkBlock($result->getBlocks(), $block, $node)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param string $areaId
     * @param array  $areas
     *
     * @return Area|null
     */
    protected function AreaIdExist($areaId, $areas)
    {
        if (!empty($areas)) {
            foreach ($areas as $area) {
                $result = $this->checkArea($areaId, $area);
                if ( null != $result) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * @param string        $areaId
     * @param AreaInterface $area
     *
     * @return Area|null
     */
    protected function checkArea($areaId, $area)
    {
        if ($areaId === $area->getAreaId()) {
            return $area;
        } else {
            return $this->AreaIdExist($areaId, $area->getAreas());
        }
    }

    /**
     * @param array          $refBlocks
     * @param BlockInterface $block
     * @param NodeInterface  $node
     *
     * @return bool
     */
    protected function checkBlock($refBlocks, $block, $node)
    {
        foreach ($refBlocks as $refBlock) {
            if ( $node->getId() === $refBlock['nodeId'] || 0 === $refBlock['nodeId']) {
                $blockRef = $node->getBlock($refBlock['blockId']);
            } else {
                $otherNode = $this->nodeRepository->find($refBlock['nodeId']);
                $blockRef = $otherNode->getBlock($refBlock['blockId']);
            }

            if ($blockRef->getLabel() === $block->getLabel()) {
                return true;
            }
        }

        return false;
    }
}
