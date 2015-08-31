<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\NodeEvents;

/**
 * Class LogNodeSubscriber
 */
class LogNodeSubscriber extends AbstractLogSubscriber
{
    /**
     * @param NodeEvent $event
     */
    public function nodeCreation(NodeEvent $event)
    {
        $this->info('open_orchestra_log.node.create', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeUpdate(NodeEvent $event)
    {
        $this->info('open_orchestra_log.node.update', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeDelete(NodeEvent $event)
    {
        $this->info('open_orchestra_log.node.delete', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeRestore(NodeEvent $event)
    {
        $this->info('open_orchestra_log.node.restore', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeDuplicate(NodeEvent $event)
    {
        $this->info('open_orchestra_log.node.duplicate', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeAddLanguage(NodeEvent $event)
    {
        $this->info('open_orchestra_log.node.add_language', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeUpdateBlock(NodeEvent $event)
    {
        $this->info('open_orchestra_log.node.block.update', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeUpdateBlockPosition(NodeEvent $event)
    {
        $this->info('open_orchestra_log.node.block.update_position', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeDeleteArea(NodeEvent $event)
    {
        $this->info('open_orchestra_log.node.area.delete', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeUpdateArea(NodeEvent $event)
    {
        $this->info('open_orchestra_log.node.area.update', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeChangeStatus(NodeEvent $event)
    {
        $this->info('open_orchestra_log.node.status', $event->getNode());
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            NodeEvents::NODE_DELETE => 'nodeDelete',
            NodeEvents::NODE_RESTORE => 'nodeRestore',
            NodeEvents::NODE_UPDATE => 'nodeUpdate',
            NodeEvents::NODE_CREATION => 'nodeCreation',
            NodeEvents::NODE_DUPLICATE => 'nodeDuplicate',
            NodeEvents::NODE_DELETE_AREA => 'nodeDeleteArea',
            NodeEvents::NODE_UPDATE_AREA => 'nodeUpdateArea',
            NodeEvents::NODE_ADD_LANGUAGE => 'nodeAddLanguage',
            NodeEvents::NODE_UPDATE_BLOCK => 'nodeUpdateBlock',
            NodeEvents::NODE_CHANGE_STATUS => 'nodeChangeStatus',
            NodeEvents::NODE_UPDATE_BLOCK_POSITION => 'nodeUpdateBlockPosition',
        );
    }

    /**
     * @param string        $message
     * @param NodeInterface $node
     */
    protected function info($message, NodeInterface $node)
    {
        $this->logger->info($message, array(
            'node_id' => $node->getNodeId(),
            'node_version' => $node->getVersion(),
            'node_language' => $node->getLanguage(),
            'node_name' => $node->getName(),
        ));
    }
}
