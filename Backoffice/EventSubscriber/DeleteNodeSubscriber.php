<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\NodeDeleteEvent;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\NodeEvents;

/**
 * Class DeleteNodeSubscriber
 */
class DeleteNodeSubscriber extends AbstractDeleteSubscriber
{
    /**
     * @param NodeDeleteEvent $event
     */
    public function addNodeTrashCan(NodeDeleteEvent $event)
    {
        $name = $event->getNodeId();
        $type = NodeInterface::TRASH_ITEM_TYPE;
        $this->createTrashItem($event->getNodeId(), $event->getSiteId(), $name, $type);
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
