<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogSiteSubscriber;
use PHPOrchestra\ModelInterface\SiteEvents;

/**
 * Class LogSiteSubscriberTest
 */
class LogSiteSubscriberTest extends LogAbstractSubscriberTest
{
    protected $site;
    protected $siteEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->site = Phake::mock('PHPOrchestra\ModelBundle\Document\Site');
        $this->siteEvent = Phake::mock('PHPOrchestra\ModelInterface\Event\SiteEvent');
        Phake::when($this->siteEvent)->getSite()->thenReturn($this->site);

        $this->subscriber = new LogSiteSubscriber($this->logger);
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
        $this->assertEventLogged('php_orchestra_log.site.create', array(
            'site_id' => $this->site->getSiteId(),
            'site_name' => $this->site->getName()
        ));
    }

    /**
     * Test siteDelete
     */
    public function testSiteDelete()
    {
        $this->subscriber->siteDelete($this->siteEvent);
        $this->assertEventLogged('php_orchestra_log.site.delete', array(
            'site_id' => $this->site->getSiteId(),
            'site_name' => $this->site->getName()
        ));
    }

    /**
     * Test siteUpdate
     */
    public function testSiteUpdate()
    {
        $this->subscriber->siteUpdate($this->siteEvent);
        $this->assertEventLogged('php_orchestra_log.site.update', array(
            'site_id' => $this->site->getSiteId(),
            'site_name' => $this->site->getName()
        ));
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
