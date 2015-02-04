<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogSiteSubscriber;
use PHPOrchestra\ModelInterface\SiteEvents;

/**
 * Class LogSiteSubscriberTest
 */
class LogSiteSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogSiteSubscriber
     */
    protected $subscriber;

    protected $logger;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
        $this->subscriber = new LogSiteSubscriber($this->logger);
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
            array(SiteEvents::SITE_CREATE),
            array(SiteEvents::SITE_DELETE),
            array(SiteEvents::SITE_UPDATE),
        );
    }
}
