<?php

namespace PHPOrchestra\LogBundle\EventSubscriber;

use PHPOrchestra\ModelInterface\Event\NodeEvent;
use PHPOrchestra\ModelInterface\Model\NodeInterface;
use PHPOrchestra\ModelInterface\NodeEvents;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogNodeSubscriber
 */
class LogNodeSubscriber implements EventSubscriberInterface
{
    protected $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeCreation(NodeEvent $event)
    {
        $node = $event->getNode();
        $this->logger->info('php_orchestra_log.node.create', array('node_id' => $node->getNodeId()));
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeUpdate(NodeEvent $event)
    {
        $this->info('php_orchestra_log.node.update', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeDelete(NodeEvent $event)
    {
        $this->info('php_orchestra_log.node.delete', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeDuplicate(NodeEvent $event)
    {
        $this->info('php_orchestra_log.node.duplicate', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeAddLanguage(NodeEvent $event)
    {
        $this->info('php_orchestra_log.node.add_language', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeUpdateBlock(NodeEvent $event)
    {
        $this->info('php_orchestra_log.node.block.update', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeUpdateBlockPosition(NodeEvent $event)
    {
        $this->info('php_orchestra_log.node.block.update_position', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeDeleteArea(NodeEvent $event)
    {
        $this->info('php_orchestra_log.node.area.delete', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeUpdateArea(NodeEvent $event)
    {
        $this->info('php_orchestra_log.node.area.update', $event->getNode());
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeChangeStatus(NodeEvent $event)
    {
        $this->info('php_orchestra_log.node.status', $event->getNode());
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            NodeEvents::NODE_DELETE => 'nodeDelete',
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
