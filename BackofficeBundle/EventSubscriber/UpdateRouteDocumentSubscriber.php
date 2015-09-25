<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\BackofficeBundle\Manager\RouteDocumentManager;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\NodeEvents;
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
     * @param NodeEvent $event
     */
    public function updateRouteDocument(NodeEvent $event)
    {
        $node = $event->getNode();
        if (!$node->getStatus()->isPublished()) {
            return;
        }

        $routesToClear = $this->routeDocumentManager->clearForNode($node);
        foreach ($routesToClear as $route) {
            $this->objectManager->remove($route);
        }

        $routes = $this->routeDocumentManager->createForNode($node);
        foreach ($routes as $routeDocument) {
            $this->objectManager->persist($routeDocument);
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
        );
    }
}
