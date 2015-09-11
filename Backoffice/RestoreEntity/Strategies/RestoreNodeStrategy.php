<?php

namespace OpenOrchestra\Backoffice\RestoreEntity\Strategies;

use OpenOrchestra\Backoffice\RestoreEntity\RestoreEntityInterface;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class RestoreNodeStrategy
 */
class RestoreNodeStrategy implements RestoreEntityInterface
{
    protected $nodeRepository;
    protected $eventDispatcher;

    /**
     * @param NodeRepositoryInterface  $nodeRepository
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        NodeRepositoryInterface $nodeRepository,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->nodeRepository = $nodeRepository;
        $this->eventDispatcher = $eventDispatcher;
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
        $nodes = $this->nodeRepository->findByNodeIdAndSiteId($node->getNodeId(), $node->getSiteId());
        /** @var NodeInterface $node */
        foreach ($nodes as $node) {
            $node->setDeleted(false);
        }
        $this->eventDispatcher->dispatch(NodeEvents::NODE_RESTORE, new NodeEvent($node));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'restore_node';
    }
}
