<?php

namespace PHPOrchestra\BackofficeBundle\Manager;

use PHPOrchestra\ModelBundle\Model\AreaContainerInterface;
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
                    $block->removeAreaRef($areaId, $node->getNodeId());
                } else {
                    $blockNode = $this->nodeRepository->findOneByNodeIdAndSiteIdAndLastVersion($blockReference['nodeId']);
                    $block = $blockNode->getBlock($blockReference['blockId']);
                    $block->removeAreaRef($areaId, $node->getNodeId());
                }
            }
        }
    }
}
