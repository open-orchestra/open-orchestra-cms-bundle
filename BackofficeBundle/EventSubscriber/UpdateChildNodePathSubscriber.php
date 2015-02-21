<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateChildNodePathSubscriber
 */
class UpdateChildNodePathSubscriber implements EventSubscriberInterface
{
    protected $nodeRepository;
    protected $container;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     * @param Container               $container
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, Container $container)
    {
        $this->nodeRepository = $nodeRepository;
        $this->container = $container;
    }

    /**
     * @param NodeEvent $event
     */
    public function updatePath(NodeEvent $event)
    {
        $node = $event->getNode();
        $parentPath = $node->getPath();

        $sons = $this->nodeRepository->findByParentIdAndSiteId($node->getNodeId());

        $sonsToUpdate = array();
        foreach ($sons as $son) {
            $son->setPath($parentPath . '/' . $son->getNodeId());
            $sonsToUpdate[$son->getNodeId()] = $son;
        }

        foreach ($sonsToUpdate as $sonToUpdate) {
            $event = new NodeEvent($sonToUpdate);
            $this->container->get('event_dispatcher')->dispatch(NodeEvents::PATH_UPDATED, $event);
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
