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
        $parentNode = $event->getNode();
        $nodesVersions = $this->nodeRepository->findNodeIdByIncludedPathSiteId($parentNode->getPath(), $parentNode->getSiteId());

        foreach ($nodesVersions as $nodeVersion) {
            $oldPath = $nodeVersion->getPath();
            $nodeVersion->setPath($parentNode->getPath() . '/' . $nodeVersion->getNodeId());
            $event = new NodeEvent($nodeVersion);
            $event->setPreviousPath($oldPath);
            $this->eventDispatcher->dispatch(NodeEvents::CHILD_PATH_UPDATED, $event);
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            NodeEvents::PATH_UPDATED => 'updatePath',
        );
    }
}
