<?php

namespace PHPOrchestra\BackOfficeBundle\Test\Context;

use Phake;
use PHPOrchestra\Backoffice\Context\ContextManager;

/**
 * Unit tests of contextManager
 */
class ContextManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $session;
    protected $contextManager;
    protected $siteRepository;

    /**
     * Tests setup
     */
    public function setUp()
    {
        $this->session = Phake::mock('Symfony\Component\HttpFoundation\Session\Session');
        $this->siteRepository = Phake::mock('PHPOrchestra\ModelBundle\Repository\SiteRepository');
        $this->contextManager = new ContextManager($this->session, $this->siteRepository);
    }

    /**
     * @param string $locale
     *
     * @dataProvider getLocale
     */
    public function testGetCurrentLocale($locale)
    {
        Phake::when($this->session)->get(ContextManager::KEY_LOCALE)->thenReturn($locale);

        $localReturned = $this->contextManager->getCurrentLocale();

        $this->assertEquals($locale, $localReturned);
        Phake::verify($this->session, Phake::times(2))->get(ContextManager::KEY_LOCALE);
    }

    /**
     * @param string $locale
     *
     * @dataProvider getLocale
     */
    public function testSetCurrentLocale($locale)
    {
        $this->contextManager->setCurrentLocale($locale);

        Phake::verify($this->session)->set(ContextManager::KEY_LOCALE, $locale);
    }

    /**
     * @param array $siteList
     * @param array $expectedArray
     *
     * @dataProvider getAvailableSites
     */
    public function testGetAvailableSites($siteList, $expectedArray)
    {
        Phake::when($this->siteRepository)->findByDeleted(Phake::anyParameters())->thenReturn($siteList);

        $this->assertEquals($expectedArray, $this->contextManager->getAvailableSites());
    }

    /**
     * @param array $site
     *
     * @dataProvider getSite
     */
    public function testSetCurrentSite($site)
    {
        $this->contextManager->setCurrentSite($site['siteId'], $site['domain'], $site['defaultLanguage']);

        Phake::verify($this->session)->set(ContextManager::KEY_SITE, array(
            'siteId' => $site['siteId'],
            'domain' => $site['domain'],
            'defaultLanguage' => $site['defaultLanguage'],
        ));
    }

    /**
     * @param array  $site
     * @param string $siteId
     *
     * @dataProvider getSiteId
     */
    public function testGetCurrentSiteId($site, $siteId)
    {
        Phake::when($this->session)->get(Phake::anyParameters())->thenReturn($site);

        $this->assertEquals($siteId, $this->contextManager->getCurrentSiteId());

        Phake::verify($this->session)->get(ContextManager::KEY_SITE);
    }

    /**
     * @param array  $site
     * @param string $domain
     *
     * @dataProvider getSiteDomain
     */
    public function testGetCurrentDomain($site, $domain)
    {
        Phake::when($this->session)->get(Phake::anyParameters())->thenReturn($site);

        $this->assertEquals($domain, $this->contextManager->getCurrentSiteDomain());

        Phake::verify($this->session)->get(ContextManager::KEY_SITE);
    }

    /**
     * @param array  $site
     * @param string $domain
     *
     * @dataProvider getSiteDefaultLanguage
     */
    public function testGetCurrentSiteDefaultLanguage($site, $domain)
    {
        Phake::when($this->session)->get(Phake::anyParameters())->thenReturn($site);

        $this->assertEquals($domain, $this->contextManager->getCurrentSiteDefaultLanguage());

        Phake::verify($this->session)->get(ContextManager::KEY_SITE);
    }

    /**
     * Locale provider
     *
     * @return array
     */
    public function getLocale()
    {
        return array(
            array(''),
            array('fr'),
            array(3),
            array('fakeKey' => 'fakeValue')
        );
    }

    /**
     * SiteId provider
     *
     * @return array
     */
    public function getSite()
    {
        return array(
            array(array('siteId' => 'fakeId', 'domain' => 'fakeDomain', 'defaultLanguage' => 'en')),
            array(array('siteId' => 'id', 'domain' => 'domain', 'defaultLanguage' => 'en')),
        );
    }

    /**
     * SiteId provider
     *
     * @return array
     */
    public function getSiteId()
    {
        return array(
            array(array('siteId' => 'fakeId', 'domain' => 'fakeDomain', 'defaultLanguage' => 'en'), 'fakeId'),
            array(array('siteId' => 'id', 'domain' => 'domain', 'defaultLanguage' => 'en'), 'id'),
        );
    }

    /**
     * SiteId provider
     *
     * @return array
     */
    public function getSiteDomain()
    {
        return array(
            array(array('siteId' => 'fakeId', 'domain' => 'fakeDomain'), 'fakeDomain'),
            array(array('siteId' => 'id', 'domain' => 'domain'), 'domain'),
        );
    }

    /**
     * SiteId provider
     *
     * @return array
     */
    public function getSiteDefaultLanguage()
    {
        return array(
            array(array('siteId' => 'fakeId', 'domain' => 'fakeDomain', 'defaultLanguage' => 'en'), 'en'),
            array(array('siteId' => 'id', 'domain' => 'domain', 'defaultLanguage' => 'fr'), 'fr'),
        );
    }

    /**
     * Available sites provider
     *
     * @return array
     */
    public function getAvailableSites()
    {
        $site1 = Phake::mock('PHPOrchestra\ModelBundle\Model\SiteInterface');
        $site2 = Phake::mock('PHPOrchestra\ModelBundle\Model\SiteInterface');

        $siteId1 = 'siteId';
        $domain1 = 'domain';

        Phake::when($site1)->getSiteId()->thenReturn($siteId1);
        Phake::when($site1)->getDomain()->thenReturn($domain1);
        Phake::when($site2)->getSiteId()->thenReturn('siteId2');
        Phake::when($site2)->getDomain()->thenReturn('domain2');

        return array(
            array(
                array($site1, $site2),
                array(
                    $site1,
                    $site2
                )
            )
        );
    }
}
