<?php

namespace OpenOrchestra\BackOfficeBundle\Tests\Context;

use Phake;
use OpenOrchestra\Backoffice\Context\ContextManager;

/**
 * Unit tests of contextManager
 */
class ContextManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContextManager
     */
    protected $contextManager;

    protected $token;
    protected $session;
    protected $tokenStorage;
    protected $defaultLocale;

    /**
     * Tests setup
     */
    public function setUp()
    {
        $this->token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->tokenStorage = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        Phake::when($this->tokenStorage)->getToken()->thenReturn($this->token);

        $this->session = Phake::mock('Symfony\Component\HttpFoundation\Session\Session');

        $this->defaultLocale = 'en';

        $this->contextManager = new ContextManager($this->session, $this->tokenStorage, $this->defaultLocale);
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
        Phake::verify($this->session, Phake::times(1))->get(ContextManager::KEY_LOCALE);
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
     * Locale provider
     *
     * @return array
     */
    public function getLocale()
    {
        return array(
            array('fr'),
            array(3),
            array('fakeKey' => 'fakeValue')
        );
    }

    /**
     * Test getAvailableSites
     */
    public function testGetAvailableSites()
    {
        $site1 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        $group1 = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');
        Phake::when($group1)->getSite()->thenReturn($site1);
        $site2 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        $group2 = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');
        Phake::when($group2)->getSite()->thenReturn($site2);
        $groups = array($group1, $group2);
        $user = Phake::mock('OpenOrchestra\UserBundle\Document\User');
        Phake::when($user)->getGroups()->thenReturn($groups);

        Phake::when($this->token)->getUser()->thenReturn($user);

        $this->assertEquals(array($site1, $site2), $this->contextManager->getAvailableSites());
    }

    /**
     * Test with no user
     */
    public function testGetAvailableSitesIfNoUser()
    {
        Phake::when($this->token)->getUser()->thenReturn(null);

        $this->assertEmpty($this->contextManager->getAvailableSites());
    }

    /**
     * @param array $site
     *
     * @dataProvider getSite
     */
    public function testSetCurrentSite($site)
    {
        $this->contextManager->setCurrentSite($site['siteId'], $site['name'], $site['defaultLanguage']);

        Phake::verify($this->session)->set(ContextManager::KEY_SITE, array(
            'siteId' => $site['siteId'],
            'name' => $site['name'],
            'defaultLanguage' => $site['defaultLanguage'],
        ));
    }

    /**
     * SiteId provider
     *
     * @return array
     */
    public function getSite()
    {
        return array(
            array(array('siteId' => 'fakeId', 'name' => 'fakeName', 'defaultLanguage' => 'en')),
            array(array('siteId' => 'id', 'name' => 'name', 'defaultLanguage' => 'en')),
        );
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

        Phake::verify($this->session, Phake::times(1))->get(ContextManager::KEY_SITE);
    }

    /**
     * SiteId provider
     *
     * @return array
     */
    public function getSiteId()
    {
        return array(
            array(array('siteId' => 'fakeId', 'name' => 'fakeName', 'defaultLanguage' => 'en'), 'fakeId'),
            array(array('siteId' => 'id', 'name' => 'name', 'defaultLanguage' => 'en'), 'id'),
        );
    }

    /**
     * @param array  $site
     * @param string $domain
     *
     * @dataProvider getSiteName
     */
    public function testGetCurrentName($site, $domain)
    {
        Phake::when($this->session)->get(Phake::anyParameters())->thenReturn($site);

        $this->assertEquals($domain, $this->contextManager->getCurrentSiteName());

        Phake::verify($this->session, Phake::times(1))->get(ContextManager::KEY_SITE);
    }

    /**
     * SiteId provider
     *
     * @return array
     */
    public function getSiteName()
    {
        return array(
            array(array('siteId' => 'fakeId', 'name' => 'fakeName'), 'fakeName'),
            array(array('siteId' => 'id', 'name' => 'name'), 'name'),
        );
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

        Phake::verify($this->session, Phake::times(1))->get(ContextManager::KEY_SITE);
    }

    /**
     * SiteId provider
     *
     * @return array
     */
    public function getSiteDefaultLanguage()
    {
        return array(
            array(array('siteId' => 'fakeId', 'name' => 'fakeName', 'defaultLanguage' => 'en'), 'en'),
            array(array('siteId' => 'id', 'name' => 'name', 'defaultLanguage' => 'fr'), 'fr'),
        );
    }

}
