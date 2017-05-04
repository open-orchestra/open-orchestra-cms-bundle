<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\Manager\RouteDocumentManager;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Event\RedirectionEvent;
use OpenOrchestra\ModelInterface\Event\SiteEvent;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\RedirectionEvents;
use OpenOrchestra\ModelInterface\SiteEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateRouteDocumentSubscriber
 */
class UpdateRouteDocumentSubscriber implements EventSubscriberInterface
{
    protected $objectManager;
    protected $routeDocumentManager;

    /**
     * @param ObjectManager        $objectManager
     * @param RouteDocumentManager $routeDocumentManager
     */
    public function __construct(ObjectManager $objectManager, RouteDocumentManager $routeDocumentManager)
    {
        $this->objectManager = $objectManager;
        $this->routeDocumentManager = $routeDocumentManager;
    }

    /**
     * @param RedirectionEvent $event
     */
    public function createOrUpdateForRedirection(RedirectionEvent $event)
    {
        $routes = $this->routeDocumentManager->createOrUpdateForRedirection($event->getRedirection());

        foreach ($routes as $route) {
            $this->objectManager->persist($route);
        }
        $this->objectManager->flush();
    }

    /**
     * @param RedirectionEvent $event
     */
    public function deleteForRedirection(RedirectionEvent $event)
    {
        $this->routeDocumentManager->deleteForRedirection($event->getRedirection());
    }

    /**
     * @param NodeEvent $event
     */
    public function deleteRouteDocument(NodeEvent $event)
    {
        $this->routeDocumentManager->clearForNode($event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function updateRouteDocument(NodeEvent $event)
    {
        $node = $event->getNode();

        if (true === $node->getStatus()->isPublishedState() ||
            true === $event->getPreviousStatus()->isPublishedState()
        ) {
            $this->routeDocumentManager->clearForNode($node);
            $routes = $this->routeDocumentManager->createForNode($node);
            foreach ($routes as $routeDocument) {
                $this->objectManager->persist($routeDocument);
            }
            $this->objectManager->flush();
        }
    }

    /**
     * @param SiteEvent $event
     */
    public function updateRouteDocumentOnSiteUpdate(SiteEvent $event)
    {
        $site = $event->getSite();
        $this->routeDocumentManager->clearForSite($site);

        $routes = $this->routeDocumentManager->createForSite($site);
        foreach ($routes as $routeDocument) {
            $this->objectManager->persist($routeDocument);
        }

        $this->objectManager->flush();
    }

    /**
     * @param SiteEvent $event
     */
    public function deleteRouteDocumentOnSiteDelete(SiteEvent $event)
    {
        $this->routeDocumentManager->clearForSite($event->getSite());
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            NodeEvents::NODE_CHANGE_STATUS => 'updateRouteDocument',
            RedirectionEvents::REDIRECTION_CREATE => 'createOrUpdateForRedirection',
            RedirectionEvents::REDIRECTION_UPDATE => 'createOrUpdateForRedirection',
            RedirectionEvents::REDIRECTION_DELETE => 'deleteForRedirection',
            SiteEvents::SITE_UPDATE => 'updateRouteDocumentOnSiteUpdate',
            SiteEvents::SITE_DELETE => 'deleteRouteDocumentOnSiteDelete',
        );
    }
}
