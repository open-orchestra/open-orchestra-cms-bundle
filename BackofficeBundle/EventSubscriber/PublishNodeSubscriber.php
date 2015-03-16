<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\DisplayBundle\Manager\CacheableManager;

/**
 * Class PublishNodeSubscriber
 */
class PublishNodeSubscriber implements EventSubscriberInterface
{
    protected $cacheableManager;

    /**
     * @param CacheableManager $cacheableManager
     */
    public function __construct(CacheableManager $cacheableManager)
    {
        $this->cacheableManager = $cacheableManager;
    }

    public function nodeChangeStatus(NodeEvent $event)
    {
        $node = $event->getNode();
        $this->cacheableManager->invalidateTags(array('node-' . $node->getNodeId()));
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            NodeEvents::NODE_CHANGE_STATUS => 'nodeChangeStatus'
        );
    }
}
