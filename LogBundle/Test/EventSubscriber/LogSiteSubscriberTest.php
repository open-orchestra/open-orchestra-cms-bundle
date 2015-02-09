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

    protected $site;
    protected $logger;
    protected $siteEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->site = Phake::mock('PHPOrchestra\ModelBundle\Document\Site');
        $this->siteEvent = Phake::mock('PHPOrchestra\ModelInterface\Event\SiteEvent');
        Phake::when($this->siteEvent)->getSite()->thenReturn($this->site);
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

    /**
     * Test siteCreate
     */
    public function testSiteCreate()
    {
        $this->subscriber->siteCreate($this->siteEvent);
        $this->eventTest();
    }

    /**
     * Test siteDelete
     */
    public function testSiteDelete()
    {
        $this->subscriber->siteDelete($this->siteEvent);
        $this->eventTest();
    }

    /**
     * Test siteUpdate
     */
    public function testSiteUpdate()
    {
        $this->subscriber->siteUpdate($this->siteEvent);
        $this->eventTest();
    }

    /**
     * Test the siteEvent
     */
    public function eventTest()
    {
        Phake::verify($this->siteEvent)->getSite();
        Phake::verify($this->logger)->info(Phake::anyParameters());
        Phake::verify($this->site)->getSiteId();
    }
}
