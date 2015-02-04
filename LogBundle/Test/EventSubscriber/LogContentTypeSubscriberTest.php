<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogContentTypeSubscriber;
use PHPOrchestra\ModelInterface\ContentTypeEvents;

/**
 * Class LogContentTypeSubscriberTest
 */
class LogContentTypeSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogContentTypeSubscriber
     */
    protected $subscriber;

    protected $logger;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
        $this->subscriber = new LogContentTypeSubscriber($this->logger);
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
            array(ContentTypeEvents::CONTENT_TYPE_CREATE),
            array(ContentTypeEvents::CONTENT_TYPE_DELETE),
            array(ContentTypeEvents::CONTENT_TYPE_UPDATE),
        );
    }
}
