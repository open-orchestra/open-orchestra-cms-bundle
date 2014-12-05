<?php

namespace PHPOrchestra\BackofficeBundle\Manager;

use PHPOrchestra\ModelBundle\Model\AreaContainerInterface;
use PHPOrchestra\ModelBundle\Model\AreaInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;
use PHPOrchestra\ModelBundle\Model\NodeInterface;
use PHPOrchestra\ModelBundle\Repository\NodeRepository;

/**
 * Class AreaManager
 */
class AreaManager
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
     * Remove an area from an AreaCollections
     *
     * @param AreaContainerInterface $areaContainer
     * @param string                 $areaId
     *
     * @return AreaContainerInterface
     */
    public function deleteAreaFromAreas(AreaContainerInterface $areaContainer, $areaId)
    {
        $areaContainer->removeAreaByAreaId($areaId);

        return $areaContainer;
    }

    /**
     * @param array         $oldBlocks
     * @param array         $newBlocks
     * @param string        $areaId
     * @param NodeInterface $node
     */
    public function deleteAreaFromBlock($oldBlocks, $newBlocks, $areaId, NodeInterface $node)
    {
        foreach ($oldBlocks as $blockReference) {
            if (!in_array($blockReference, $newBlocks)) {
                if ($blockReference['nodeId'] === 0) {
                    $block = $node->getBlock($blockReference['blockId']);
                    $block->removeAreaRef($areaId, $node->getId());
                } else {
                    $blockNode = $this->nodeRepository->find($blockReference['nodeId']);
                    $block = $blockNode->getBlock($blockReference['blockId']);
                    $block->removeAreaRef($areaId, $node->getId());
                }
            }
        }
    }

    /**
     * @param NodeInterface $node
     *
     * @return bool
     */
    public function areaConsistency($node)
    {
        foreach ($node->getAreas() as $area) {
            if (!$this->checkBlockRef($area->getBlocks(), $node, $area)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array         $blocks
     * @param NodeInterface $node
     * @param AreaInterface $area
     *
     * @return bool
     */
    protected function checkBlockRef($blocks, $node, $area)
    {
        foreach ($blocks as $block) {
            if ($block['nodeId'] === $node->getNodeId() || $block['nodeId'] === 0) {
                if (!$this->blockIdExist($node->getBlock($block['blockId']), $area->getAreaId())) {
                    return false;
                }
            } else {
                $otherNode = $this->nodeRepository->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($block['nodeId']);
                if (!$this->blockIdExist($otherNode->getBlock($block['blockId']), $area->getAreaId())) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param BlockInterface $block
     * @param string         $areaId
     *
     * @return bool
     */
    protected function blockIdExist($block, $areaId)
    {
        $areas = $block->getAreas();

        foreach ($areas as $area) {
            if ($area['areaId'] === $areaId) {
                return true;
            }
        }

        return false;
    }
}
