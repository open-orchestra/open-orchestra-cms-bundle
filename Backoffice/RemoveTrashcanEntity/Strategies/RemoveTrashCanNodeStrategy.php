<?php

namespace OpenOrchestra\Backoffice\RemoveTrashcanEntity\Strategies;

use OpenOrchestra\Backoffice\Manager\NodeManager;
use OpenOrchestra\Backoffice\RemoveTrashcanEntity\RemoveTrashCanEntityInterface;
use OpenOrchestra\ModelInterface\Event\TrashcanEvent;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\TrashcanEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class RemoveTrashCanNodeStrategy
 */
class RemoveTrashCanNodeStrategy implements RemoveTrashCanEntityInterface
{
    protected $nodeRepository;
    protected $eventDispatcher;
    protected $nodeManager;

    /**
     * @param NodeRepositoryInterface      $nodeRepository
     * @param EventDispatcherInterface     $eventDispatcher
     * @param NodeManager                  $nodeManager
     */
    public function __construct(
        NodeRepositoryInterface $nodeRepository,
        EventDispatcherInterface $eventDispatcher,
        NodeManager $nodeManager
    ){
        $this->nodeRepository = $nodeRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->nodeManager = $nodeManager;
    }

    /**
     * @param TrashItemInterface $trashItem
     *
     * @return bool
     */
    public function support(TrashItemInterface $trashItem)
    {
        return NodeInterface::ENTITY_TYPE === $trashItem->getType();
    }

    /**
     * @param TrashItemInterface $trashItem
     */
    public function remove(TrashItemInterface $trashItem)
    {
        $nodes = $this->nodeRepository->findByNodeAndSite($trashItem->getEntityId(), $trashItem->getSiteId());
        $nodeIds = array();
        /** @var NodeInterface $node */
        foreach ($nodes as $node) {
                $this->nodeManager->deleteBlockInNode($node);
                $nodeIds[] = $node->getId();
                $this->eventDispatcher->dispatch(TrashcanEvents::TRASHCAN_REMOVE_ENTITY, new TrashcanEvent($node));
        }
        $this->nodeRepository->removeNodeVersions($nodeIds);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'remove_trashcan_node';
    }
}
