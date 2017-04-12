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
    protected $currentSiteManager;

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
        $events = array();
        $nodesVersions = $this->nodeRepository->findByParent($parentNode->getNodeId(), $parentNode->getSiteId());

        foreach ($nodesVersions as $nodeVersion) {
            $oldPath = $nodeVersion->getPath();
            $nodeVersion->setPath($parentNode->getPath() . '/' . $nodeVersion->getNodeId());

            if (!isset($events[$nodeVersion->getNodeId()])) {
                $event = new NodeEvent($nodeVersion);
                $event->setPreviousPath($oldPath);
                $events[$nodeVersion->getNodeId()] = $event;
            }
        }

        foreach ($events as $event) {
            $this->eventDispatcher->dispatch(NodeEvents::PATH_UPDATED, $event);
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
