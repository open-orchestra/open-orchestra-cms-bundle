<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\BackofficeBundle\Manager\RouteDocumentManager;
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
            RedirectionEvents::REDIRECTION_DELETE => 'createOrUpdateForRedirection',
            SiteEvents::SITE_UPDATE => 'updateRouteDocumentOnSiteUpdate',
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
