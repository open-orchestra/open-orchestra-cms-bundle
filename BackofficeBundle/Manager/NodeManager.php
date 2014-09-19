<?php

namespace PHPOrchestra\BackofficeBundle\Manager;

use PHPOrchestra\ModelBundle\Model\NodeInterface;
use PHPOrchestra\ModelBundle\Repository\NodeRepository;

/**
 * Class NodeManager
 */

class NodeManager
{

    /**
     * Duplicate a node
     * 
     * @param NodeInterface $node
     */
    public function duplicateNode(NodeInterface $node)
    {
        $newNode = clone $node;
        $newNode->setVersion($node->getVersion() + 1);
        
        return $newNode;
    }

    /**
     * @param NodeInterface  $node
     * @param NodeRepository $nodeRepository
     */
    protected function deleteTree(NodeInterface $node, NodeRepository $nodeRepository)
    {
        $node->setDeleted(true);
    
        $sons = $nodeRepository->findByParentId($node->getNodeId());
    
        foreach ($sons as $son) {
            $this->deleteTree($son);
        }
    }
}
