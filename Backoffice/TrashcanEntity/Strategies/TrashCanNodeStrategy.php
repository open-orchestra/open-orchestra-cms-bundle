<?php

namespace OpenOrchestra\Backoffice\TrashcanEntity\Strategies;

use OpenOrchestra\Backoffice\Manager\NodeManager;
use OpenOrchestra\Backoffice\TrashcanEntity\TrashCanEntityInterface;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Event\TrashcanEvent;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\TrashcanEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class TrashCanNodeStrategy
 */
class TrashCanNodeStrategy implements TrashCanEntityInterface
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
        return NodeInterface::TRASH_ITEM_TYPE === $trashItem->getType();
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
     * @param TrashItemInterface $trashItem
     */
    public function restore(TrashItemInterface $trashItem)
    {
        $nodes = $this->nodeRepository->findByNodeAndSite($trashItem->getEntityId(), $trashItem->getSiteId());
        if (count($nodes) > 0) {

            $path = null;
            $parentId = $nodes[0]->getParentId();
            $countParentNodes = $this->nodeRepository->countByParentId($parentId, $trashItem->getSiteId());
            if (0 === $countParentNodes) {
                $parentId = NodeInterface::ROOT_NODE_ID;
                $path = NodeInterface::ROOT_NODE_ID . '/'. $trashItem->getEntityId();
            }

            foreach ($nodes as $node) {
                $this->eventDispatcher->dispatch(NodeEvents::NODE_RESTORE, new NodeEvent($node));
            }
            $this->nodeRepository->restoreDeletedNode($trashItem->getEntityId(), $trashItem->getSiteId(), $parentId, $path);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'remove_trashcan_node';
    }
}
