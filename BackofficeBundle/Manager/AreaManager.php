<?php

namespace PHPOrchestra\BackofficeBundle\Manager;

use PHPOrchestra\ModelInterface\Model\AreaContainerInterface;
use PHPOrchestra\ModelInterface\Model\AreaInterface;
use PHPOrchestra\ModelInterface\Model\BlockInterface;
use PHPOrchestra\ModelInterface\Model\NodeInterface;
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
                    $blockNode = $this->nodeRepository
                        ->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($blockReference['nodeId'], $node->getLanguage(), $node->getSiteId());
                    $block = $blockNode->getBlock($blockReference['blockId']);
                    $block->removeAreaRef($areaId, $node->getId());
                }
            }
        }
    }

    /**
     * @param AreaContainerInterface $container
     * @param NodeInterface          $node
     *
     * @return bool
     */
    public function areaConsistency(AreaContainerInterface $container, $node = null)
    {
        if (is_null($node)) {
            $node = $container;
        }

        foreach ($container->getAreas() as $area) {
            if (is_array($area->getBlocks()) && count($area->getBlocks()) > 0) {
                if (!$this->checkBlockRef($area->getBlocks(), $node, $area)) {
                    return false;
                }
            } else {
                foreach ($container->getAreas() as $area) {
                    $consistency = $this->areaConsistency($area, $node);
                    if (false === $consistency) {
                        return false;
                    }
                }
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
    protected function checkBlockRef($blocks, NodeInterface $node, AreaInterface $area)
    {
        foreach ($blocks as $block) {
            if ($block['nodeId'] === $node->getNodeId() || $block['nodeId'] === 0) {
                if (!$this->areaIdExistInBlock($node->getBlock($block['blockId']), $area->getAreaId())) {
                    return false;
                }
            } else {
                $otherNode = $this->nodeRepository->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($block['nodeId'], $node->getLanguage(), $node->getSiteId());
                if (!$this->areaIdExistInBlock($otherNode->getBlock($block['blockId']), $area->getAreaId())) {
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
    protected function areaIdExistInBlock($block, $areaId)
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
