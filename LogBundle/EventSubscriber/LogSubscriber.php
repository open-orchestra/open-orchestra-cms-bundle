<?php

namespace PHPOrchestra\LogBundle\EventSubscriber;

use PHPOrchestra\ModelInterface\Event\NodeEvent;
use PHPOrchestra\ModelInterface\NodeEvents;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogSubscriber
 */
class LogSubscriber implements EventSubscriberInterface
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
        $this->logger->info('Create a new node', array('node_name' => $node->getName()));
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeUpdate(NodeEvent $event)
    {
        $node = $event->getNode();
        $this->logger->info('Update a node', array('nodeId' => $node->getNodeId(), 'node_name' => $node->getName()));
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeDelete(NodeEvent $event)
    {
        $node = $event->getNode();
        $this->logger->info('Delete a node', array('nodeId' => $node->getNodeId(), 'node_name' => $node->getName()));
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeDuplicate(NodeEvent $event)
    {
        $node = $event->getNode();
        $this->logger->info('Duplicate a node', array('nodeId' => $node->getNodeId(), 'node_name' => $node->getName(), $node->getVersion()));
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeAddLanguage(NodeEvent $event)
    {
        $node = $event->getNode();
        $this->logger->info('Add language', array('nodeId' => $node->getNodeId(), 'node_name' => $node->getName(), $node->getLanguage()));
    }

    /**
     * @param NodeEvent $event
     */
    public function nodeDeleteBlock(NodeEvent $event)
    {

    }

    /**
     * @param NodeEvent $event
     */
    public function nodeUpdateBlock(NodeEvent $event)
    {

    }

    /**
     * @param NodeEvent $event
     */
    public function nodeUpdateBlockPosition(NodeEvent $event)
    {
        $node = $event->getNode();
        $area = $event->getArea();
        $this->logger->info('Update block position', array('nodeId' => $node->getNodeId(), 'node_name' => $node->getName(), $area->getAreaId()));
    }

    public function nodeDeleteArea(NodeEvent $event)
    {
        $node = $event->getNode();
        $area = $event->getArea();
        $this->logger->info('Delete area', array('nodeId' => $node->getNodeId(), 'node_name' => $node->getName(), $area->getAreaId()));
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
            NodeEvents::NODE_ADD_LANGUAGE => 'nodeAddLanguage',
            NodeEvents::NODE_DELETE_BLOCK => 'nodeDeleteBlock',
            NodeEvents::NODE_UPDATE_BLOCK => 'nodeUpdateBlock',
            NodeEvents::NODE_UPDATE_BLOCK_POSITION => 'nodeEvent',
        );
    }
}
