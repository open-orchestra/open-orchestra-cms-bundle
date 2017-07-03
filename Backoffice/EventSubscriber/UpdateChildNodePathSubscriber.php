<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateChildNodePathSubscriber
 */
class UpdateChildNodePathSubscriber implements EventSubscriberInterface
{
    protected $nodeRepository;
    protected $eventDispatcher;

    /**
     * @param NodeRepositoryInterface  $nodeRepository
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->nodeRepository = $nodeRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param NodeEvent $event
     */
    public function updatePath(NodeEvent $event)
    {
        $node = $event->getNode();
        $nodeVersions = $this->nodeRepository->findByNodeAndSite($node->getNodeId(), $node->getSiteId());
        $parentNode = $this->nodeRepository->findOneByNodeAndSite($node->getParentId(), $node->getSiteId());
        $oldPath = $node->getPath();
        $newPath = $parentNode->getPath() . '/' . $node->getNodeId();

        foreach($nodeVersions as $nodeVersion) {
            $nodeVersion->setPath($newPath);
        }

        $event = new NodeEvent($node);
        $event->setPreviousPath($oldPath);
        $this->eventDispatcher->dispatch(NodeEvents::PATH_UPDATED, $event);

        $children = $this->nodeRepository->findNodeIdByIncludedPathSiteId($oldPath, $node->getSiteId());

        $childrenNodeId = array();
        foreach ($children as $child) {
            $childOldPath = $child->getPath();
            $childNodeId = $child->getNodeId();
            $child->setPath(preg_replace('/^' . preg_quote($oldPath, '/') . '\//', $newPath . '/', $childOldPath));
            if (!in_array($childNodeId, $childrenNodeId)) {
                $childrenNodeId[] = $childNodeId;
                $event = new NodeEvent($child);
                $event->setPreviousPath($childOldPath);
                $this->eventDispatcher->dispatch(NodeEvents::PATH_UPDATED, $event);
            }
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            NodeEvents::NODE_MOVE => 'updatePath',
        );
    }
}
