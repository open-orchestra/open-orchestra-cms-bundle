<?php

namespace PHPOrchestra\BackofficeBundle\Manager;

use PHPOrchestra\ModelInterface\Model\AreaInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use PHPOrchestra\ModelInterface\Model\NodeInterface;
use PHPOrchestra\ModelInterface\Repository\NodeRepositoryInterface;

/**
 * Class BlockManager
 */
class BlockManager
{
    protected $nodeRepository;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     */
    public function __construct(NodeRepositoryInterface $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    public function blockConsistency(NodeInterface $node)
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
    protected function checkAreaRef($refAreas, NodeInterface $nodeRef, BlockInterface $block)
    {
        foreach ($refAreas as $refArea) {
            $node = $nodeRef;
            if (!($refArea['nodeId'] === $nodeRef->getId() || $refArea['nodeId'] === 0)) {
                $otherNode = $this->nodeRepository->find($refArea['nodeId']);
                $node = $otherNode;
            }
            $result = $this->findAreaIfExist($refArea['areaId'], $node->getAreas());

            if (null === $result || !$this->checkBlock($result->getBlocks(), $block, $node)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $areaId
     * @param array  $areas
     *
     * @return AreaInterface|null
     */
    protected function findAreaIfExist($areaId, $areas)
    {
        if (!empty($areas)) {
            foreach ($areas as $area) {
                $result = $this->checkArea($areaId, $area);
                if ( !is_null($result)) {
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
     * @return AreaInterface|null
     */
    protected function checkArea($areaId, AreaInterface $area)
    {
        if ($areaId === $area->getAreaId()) {
            return $area;
        }

        return $this->findAreaIfExist($areaId, $area->getAreas());
    }

    /**
     * @param array          $refBlocks
     * @param BlockInterface $block
     * @param NodeInterface  $node
     *
     * @return bool
     */
    protected function checkBlock($refBlocks, BlockInterface $block, NodeInterface $node)
    {
        foreach ($refBlocks as $refBlock) {
            $blockRef = $node->getBlock($refBlock['blockId']);
            if (!($node->getNodeId() === $refBlock['nodeId'] || 0 === $refBlock['nodeId'])) {
                $otherNode = $this->nodeRepository
                    ->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($refBlock['nodeId'], $node->getLanguage(), $node->getSiteId());
                $blockRef = $otherNode->getBlock($refBlock['blockId']);
            }

            if ($blockRef->getLabel() === $block->getLabel()) {
                return true;
            }
        }

        return false;
    }
}
