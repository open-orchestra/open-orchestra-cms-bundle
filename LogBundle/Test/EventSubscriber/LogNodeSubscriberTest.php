<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\ModelInterface\NodeEvents;
use PHPOrchestra\LogBundle\EventSubscriber\LogNodeSubscriber;

/**
 * Test LogNodeSubscriberTest
 */
class LogNodeSubscriberTest extends LogAbstractSubscriberTest
{
    protected $nodeEvent;
    protected $node;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->node = Phake::mock('PHPOrchestra\ModelBundle\Document\Node');
        $this->nodeEvent = Phake::mock('PHPOrchestra\ModelInterface\Event\NodeEvent');
        Phake::when($this->nodeEvent)->getNode()->thenReturn($this->node);

        $this->subscriber = new LogNodeSubscriber($this->logger);
    }

    /**
     * @return array
     */
    public function provideSubscribedEvent()
    {
        return array(
            array(NodeEvents::NODE_CREATION),
            array(NodeEvents::NODE_ADD_LANGUAGE),
            array(NodeEvents::NODE_DELETE),
            array(NodeEvents::NODE_DUPLICATE),
            array(NodeEvents::NODE_UPDATE),
            array(NodeEvents::NODE_UPDATE_BLOCK),
            array(NodeEvents::NODE_UPDATE_BLOCK_POSITION),
        );
    }

    /**
     * Test nodeCreation
     */
    public function testNodeCreation()
    {
        $this->subscriber->nodeCreation($this->nodeEvent);
        $this->assertEventLogged('php_orchestra_log.node.create', array('node_id' => $this->node->getNodeId()));
    }

    /**
     * Test nodeDelete
     */
    public function testNodeDelete()
    {
        $this->subscriber->nodeDelete($this->nodeEvent);
        $this->assertEventLogged('php_orchestra_log.node.delete', array(
            'node_id' => $this->node->getNodeId(),
            'node_name' => $this->node->getName()
        ));
    }

    /**
     * Test nodeUpdate
     */
    public function testNodeUpdate()
    {
        $this->subscriber->nodeUpdate($this->nodeEvent);
        $this->assertEventLogged('php_orchestra_log.node.update', array(
            'node_id' => $this->node->getNodeId(),
            'node_version' => $this->node->getVersion(),
            'node_language' => $this->node->getLanguage()
        ));
    }

    /**
     * Test nodeDuplicate
     */
    public function testNodeDuplicate()
    {
        $this->subscriber->nodeDuplicate($this->nodeEvent);
        $this->assertEventLogged('php_orchestra_log.node.duplicate', array(
            'node_id' => $this->node->getNodeId(),
            'node_version' => $this->node->getVersion(),
            'node_language' => $this->node->getLanguage()
        ));
    }

    /**
     * Test nodeAddLanguage
     */
    public function testNodeAddLanguage()
    {
        $this->subscriber->nodeAddLanguage($this->nodeEvent);
        $this->assertEventLogged('php_orchestra_log.node.add_language', array(
            'node_id' => $this->node->getNodeId(),
            'node_name' => $this->node->getName(),
            'node_language' => $this->node->getLanguage()
        ));
    }

    /**
     * Test nodeUpdateBlock
     */
    public function testNodeUpdateBlock()
    {
        $this->subscriber->nodeUpdateBlock($this->nodeEvent);
        $this->assertEventLogged('php_orchestra_log.node.block.update', array(
            'node_id' => $this->node->getNodeId(),
            'node_language' => $this->node->getLanguage(),
            'node_version' => $this->node->getVersion()
        ));
    }

    /**
     * Test nodeUpdateBlockPosition
     */
    public function testNodeUpdateBlockPosition()
    {
        $this->subscriber->nodeUpdateBlockPosition($this->nodeEvent);
        $this->assertEventLogged('php_orchestra_log.node.block.update_position', array(
            'node_id' => $this->node->getNodeId(),
            'node_language' => $this->node->getLanguage(),
            'node_version' => $this->node->getVersion()
        ));
    }

    /**
     * Test nodeDeleteArea
     */
    public function testNodeDeleteArea()
    {
        $this->subscriber->nodeDeleteArea($this->nodeEvent);
        $this->assertEventLogged('php_orchestra_log.node.area.delete', array(
            'node_id' => $this->node->getNodeId(),
            'node_language' => $this->node->getLanguage(),
            'node_version' => $this->node->getVersion()
        ));
    }

    /**
     * Test nodeUpdateArea
     */
    public function testNodeUpdateArea()
    {
        $this->subscriber->nodeUpdateArea($this->nodeEvent);
        $this->assertEventLogged('php_orchestra_log.node.area.update', array(
            'node_id' => $this->node->getNodeId(),
            'node_language' => $this->node->getLanguage(),
            'node_version' => $this->node->getVersion()
        ));
    }

    /**
     * Test nodeChangeStatus
     */
    public function testNodeChangeStatus()
    {
        $this->subscriber->nodeChangeStatus($this->nodeEvent);
        $this->assertEventLogged('php_orchestra_log.node.status', array(
            'node_id' => $this->node->getNodeId(),
            'node_language' => $this->node->getLanguage(),
            'node_version' => $this->node->getVersion()
        ));
    }
}
