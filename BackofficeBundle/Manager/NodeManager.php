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
     * @var NodeRepository $nodeRepository
     */
    protected $nodeRepository;

    /**
     * Constructor
     *
     * @param NodeRepository $nodeRepository
     */
    public function __construct(NodeRepository $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

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
    protected function deleteTree(NodeInterface $node)
    {
        $node->setDeleted(true);
        $sons = $this->nodeRepository->findByParentId($node->getNodeId());
        foreach ($sons as &$son) {
            $son = $this->deleteTree($son);
        }
        return $node;
    }
}
