<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
<<<<<<< Updated upstream
use PHPOrchestra\BackofficeBundle\NodeEvents;
=======
use PHPOrchestra\ModelInterface\NodeEvents;
>>>>>>> Stashed changes
use PHPOrchestra\LogBundle\EventSubscriber\LogSubscriber;

/**
 * Test LogSubscriberTest
 */
class LogSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogSubscriber
     */
    protected $subscriber;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->subscriber = new LogSubscriber();
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
//            array(NodeEvents::NODE_ADD_LANGUAGE),
//            array(NodeEvents::NODE_DELETE),
//            array(NodeEvents::NODE_DUPLICATE),
//            array(NodeEvents::NODE_UPDATE),
//            array(NodeEvents::NODE_UPDATE_BLOCK),
//            array(NodeEvents::NODE_UPDATE_BLOCK_POSITION),
//            array(NodeEvents::NODE_DELETE_BLOCK),
        );
    }
}
