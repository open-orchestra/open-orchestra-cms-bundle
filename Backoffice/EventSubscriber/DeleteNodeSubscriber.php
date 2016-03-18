<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;
use OpenOrchestra\ModelInterface\NodeEvents;

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
        $name = $node->getName() . " (".$node->getLanguage()." - v".$node->getVersion().")";
        $type = TrashItemInterface::TYPE_NODE;
        $this->createTrashItem($node, $name, $type);
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
