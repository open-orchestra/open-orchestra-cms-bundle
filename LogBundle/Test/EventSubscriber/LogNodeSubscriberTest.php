<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\ModelInterface\NodeEvents;
use PHPOrchestra\LogBundle\EventSubscriber\LogNodeSubscriber;

/**
 * Test LogNodeSubscriberTest
 */
class LogNodeSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogNodeSubscriber
     */
    protected $subscriber;

    protected $nodeEvent;
    protected $logger;
    protected $node;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->node = Phake::mock('PHPOrchestra\ModelBundle\Document\Node');
        $this->nodeEvent = Phake::mock('PHPOrchestra\ModelInterface\Event\NodeEvent');
        Phake::when($this->nodeEvent)->getNode()->thenReturn($this->node);
        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
        $this->subscriber = new LogNodeSubscriber($this->logger);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * @param string $eventName
     *
     * @dataProvider provideSubscribedEvent
     */
    public function testEventSubscribed($eventName)
    {
        $this->assertArrayHasKey($eventName, $this->subscriber->getSubscribedEvents());
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
        $this->eventTest();
    }

    /**
     * Test nodeDelete
     */
    public function testNodeDelete()
    {
        $this->subscriber->nodeDelete($this->nodeEvent);
        $this->eventTest();
    }

    /**
     * Test nodeUpdate
     */
    public function testNodeUpdate()
    {
        $this->subscriber->nodeUpdate($this->nodeEvent);
        $this->eventTest();
    }

    /**
     * Test nodeDuplicate
     */
    public function testNodeDuplicate()
    {
        $this->subscriber->nodeDuplicate($this->nodeEvent);
        $this->eventTest();
    }

    /**
     * Test nodeAddLanguage
     */
    public function testNodeAddLanguage()
    {
        $this->subscriber->nodeAddLanguage($this->nodeEvent);
        $this->eventTest();
    }

    /**
     * Test nodeUpdateBlock
     */
    public function testNodeUpdateBlock()
    {
        $this->subscriber->nodeUpdateBlock($this->nodeEvent);
        $this->eventTest();
    }

    /**
     * Test nodeUpdateBlockPosition
     */
    public function testNodeUpdateBlockPosition()
    {
        $this->subscriber->nodeUpdateBlockPosition($this->nodeEvent);
        $this->eventTest();
    }

    /**
     * Test nodeDeleteArea
     */
    public function testNodeDeleteArea()
    {
        $this->subscriber->nodeDeleteArea($this->nodeEvent);
        $this->eventTest();
    }

    /**
     * Test nodeUpdateArea
     */
    public function testNodeUpdateArea()
    {
        $this->subscriber->nodeUpdateArea($this->nodeEvent);
        $this->eventTest();
    }

    /**
     * Test nodeChangeStatus
     */
    public function testNodeChangeStatus()
    {
        $this->subscriber->nodeChangeStatus($this->nodeEvent);
        $this->eventTest();
    }

    /**
     * Test the nodeEvent
     */
    public function eventTest()
    {
        Phake::verify($this->nodeEvent)->getNode();
        Phake::verify($this->logger)->info(Phake::anyParameters());
        Phake::verify($this->node)->getNodeId();
    }
}
