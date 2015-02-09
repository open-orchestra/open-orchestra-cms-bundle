<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;

/**
 * Class LogAbstractSubscriberTest
 */
abstract class LogAbstractSubscriberTest extends \PHPUnit_Framework_TestCase
{
    protected $logger;
    protected $subscriber;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
    }

    /**
     * @param string $message
     * @param array  $context
     */
    public function assertEventLogged($message, $context)
    {
        Phake::verify($this->logger)->info($message, $context);
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
    abstract public function provideSubscribedEvent();
}
