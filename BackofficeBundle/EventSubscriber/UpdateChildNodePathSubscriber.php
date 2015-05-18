<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
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
    protected $currentSiteManager;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     * @param Container               $container
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, Container $container, CurrentSiteIdInterface $currentSiteManager)
    {
        $this->nodeRepository = $nodeRepository;
        $this->container = $container;
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * @param NodeEvent $event
     */
    public function updatePath(NodeEvent $event)
    {
        $node = $event->getNode();
        $parentPath = $node->getPath();
        $siteId = $this->currentSiteManager->getCurrentSiteId();
        $sons = $this->nodeRepository->findByParentIdAndSiteId($node->getNodeId(), $siteId);

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
