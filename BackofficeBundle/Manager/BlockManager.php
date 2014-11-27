<?php

namespace PHPOrchestra\BackofficeBundle\Manager;

use PHPOrchestra\ModelBundle\Document\Area;
use PHPOrchestra\ModelBundle\Model\AreaInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use PHPOrchestra\ModelBundle\Model\NodeInterface;

/**
 * Class BlockManager
 */
class BlockManager
{
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
     * @param NodeInterface  $node
     * @param BlockInterface $block
     *
     * @return bool
     */
    protected function checkAreaRef($refAreas, $node, $block)
    {
        foreach ($refAreas as $refArea) {

            if ($refArea['nodeId'] === $node->getNodeId() || $refArea['nodeId'] === 0) {
                $result = $this->AreaIdExist($refArea['areaId'], $node->getAreas());

                if (null === $result) {
                    return false;
                } else {
                    if (!$this->checkBlock($result->getBlocks(), $block, $node)) {
                        return false;
                    }
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
            $blockRef = $node->getBlock($refBlock['blockId']);

            if ($blockRef->getLabel() === $block->getLabel()) {
                return true;
            }
        }

        return false;
    }
}
