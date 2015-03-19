<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\DisplayBundle\Manager\CacheableManager;
use OpenOrchestra\BaseBundle\Manager\TagManager;

/**
 * Class ChangeNodeStatusSubscriber
 */
class ChangeNodeStatusSubscriber implements EventSubscriberInterface
{
    protected $cacheableManager;
    protected $tagManager;

    /**
     * @param CacheableManager $cacheableManager
     * @param TagManager       $tagManager
     */
    public function __construct(CacheableManager $cacheableManager, TagManager $tagManager)
    {
        $this->cacheableManager = $cacheableManager;
        $this->tagManager = $tagManager;
    }

    /**
     * Triggered when a node status changes
     * 
     * @param NodeEvent $event
     */
    public function nodeChangeStatus(NodeEvent $event)
    {
        $node = $event->getNode();

        $this->cacheableManager->invalidateTags(
            array(
                $this->tagManager->formatNodeIdTag($node->getNodeId())
            )
        );
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
