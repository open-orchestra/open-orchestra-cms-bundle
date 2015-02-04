<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogContentSubscriber;
use PHPOrchestra\ModelInterface\ContentEvents;

/**
 * Class LogContentSubscriberTest
 */
class LogContentSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogContentSubscriber
     */
    protected $subscriber;

    protected $logger;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
        $this->subscriber = new LogContentSubscriber($this->logger);
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
            array(ContentEvents::CONTENT_CREATION),
            array(ContentEvents::CONTENT_DELETE),
            array(ContentEvents::CONTENT_DUPLICATE),
            array(ContentEvents::CONTENT_UPDATE),
        );
    }
}
