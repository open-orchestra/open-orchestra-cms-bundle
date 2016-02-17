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
            $this->objectManager->flush($route);
        }
    }

    /**
     * @param RedirectionEvent $event
     */
    public function deleteForRedirection(RedirectionEvent $event)
    {
        $routes = $this->routeDocumentManager->deleteForRedirection($event->getRedirection());

        foreach ($routes as $route) {
            $this->objectManager->remove($route);
        }
        $this->objectManager->flush();
    }

    /**
     * @param NodeEvent $event
     */
    public function deleteRouteDocument(NodeEvent $event)
    {
        $routesToClear = $this->routeDocumentManager->clearForNode($event->getNode());

        foreach ($routesToClear as $route) {
            $this->objectManager->remove($route);
        }

        $this->objectManager->flush();
    }

    /**
     * @param NodeEvent $event
     */
    public function updateRouteDocument(NodeEvent $event)
    {
        $this->updateRouteDocumentByType($event->getNode(), 'Node');
    }

    /**
     * @param SiteEvent $event
     */
    public function updateRouteDocumentOnSiteUpdate(SiteEvent $event)
    {
        $this->updateRouteDocumentByType($event->getSite(), 'Site');
    }

    /**
     * @param SiteEvent $event
     */
    public function deleteRouteDocumentOnSiteDelete(SiteEvent $event)
    {
        $routesToClear = $this->routeDocumentManager->clearForSite($event->getSite());

        foreach ($routesToClear as $route) {
            $this->objectManager->remove($route);
        }

        $this->objectManager->flush();
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
            NodeEvents::NODE_RESTORE => 'updateRouteDocument',
            NodeEvents::NODE_DELETE => 'deleteRouteDocument',
            RedirectionEvents::REDIRECTION_CREATE => 'createOrUpdateForRedirection',
            RedirectionEvents::REDIRECTION_UPDATE => 'createOrUpdateForRedirection',
            RedirectionEvents::REDIRECTION_DELETE => 'deleteForRedirection',
            SiteEvents::SITE_UPDATE => 'updateRouteDocumentOnSiteUpdate',
            SiteEvents::SITE_DELETE => 'deleteRouteDocumentOnSiteDelete',
        );
    }

    /**
     * @param mixed  $element
     * @param string $type
     */
    protected function updateRouteDocumentByType($element, $type)
    {
        $routesToClear = $this->routeDocumentManager->{'clearFor' . $type}($element);

        foreach ($routesToClear as $route) {
            $this->objectManager->remove($route);
        }

        $routes = $this->routeDocumentManager->{'createFor' . $type}($element);

        foreach ($routes as $routeDocument) {
            $this->objectManager->persist($routeDocument);
        }

        $this->objectManager->flush();
    }
}
