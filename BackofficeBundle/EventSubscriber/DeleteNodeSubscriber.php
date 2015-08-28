<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\NodeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DeleteNodeSubscriber
 */
class DeleteNodeSubscriber extends AbstractDeleteSubscriber
{
    /**
     * @param NodeEvent $event
     */
    public function addNodeTrashCan(NodeEvent $event)
    {
        $node = $event->getNode();
        $this->createTrashItem($node, $node->getName());
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            NodeEvents::NODE_DELETE => 'addNodeTrashCan',
        );
    }
}
