<?php

namespace OpenOrchestra\Backoffice\RestoreEntity\Strategies;

use OpenOrchestra\Backoffice\RestoreEntity\RestoreEntityInterface;
use OpenOrchestra\BackofficeBundle\Manager\NodeManager;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class RestoreNodeStrategy
 */
class RestoreNodeStrategy implements RestoreEntityInterface
{
    /**
     * @var NodeManager
     */
    protected $nodeManager;

    /**
     * @param NodeManager $nodeManager
     */
    public function __construct(NodeManager $nodeManager)
    {
        $this->nodeManager = $nodeManager;
    }

    /**
     * @param mixed $entity
     *
     * @return bool
     */
    public function support($entity)
    {
        return $entity instanceof NodeInterface;
    }

    /**
     * @param NodeInterface $node
     */
    public function restore($node)
    {
        $this->nodeManager->restoreNode($node);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'restore_node';
    }
}
