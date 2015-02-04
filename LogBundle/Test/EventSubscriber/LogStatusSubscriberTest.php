<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogStatusSubscriber;
use PHPOrchestra\ModelInterface\StatusEvents;

/**
 * Class LogStatusSubscriberTest
 */
class LogStatusSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogStatusSubscriber
     */
    protected $subscriber;

    protected $logger;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
        $this->subscriber = new LogStatusSubscriber($this->logger);
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
            array(StatusEvents::STATUS_CHANGE),
            array(StatusEvents::STATUS_CREATE),
            array(StatusEvents::STATUS_DELETE),
            array(StatusEvents::STATUS_UPDATE),
        );
    }
}
