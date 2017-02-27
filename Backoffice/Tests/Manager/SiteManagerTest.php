<?php

namespace OpenOrchestra\Backoffice\Tests\Manager;

use OpenOrchestra\Backoffice\Manager\SiteManager;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\SiteInterface;

/**
 * Class SiteManagerTest
 */
class SiteManagerTest extends AbstractBaseTestCase
{
    /**
     * @var SiteManager
     */
    protected $manager;

    protected $siteClass;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->siteClass = 'OpenOrchestra\ModelBundle\Document\Site';
        $this->manager = new SiteManager($this->siteClass);
    }

    /**
     * Test initializeNewSite
     */
    public function testInitializeNewSite()
    {
        $site = $this->manager->initializeNewSite();

        $this->assertEquals(SiteInterface::PRIORITY_DEFAULT, $site->getSitemapPriority());
        $this->assertEquals(SiteInterface::CHANGE_FREQ_DEFAULT, $site->getSitemapChangefreq());
    }
}
