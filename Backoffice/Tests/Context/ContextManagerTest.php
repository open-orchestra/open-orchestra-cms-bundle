<?php

namespace OpenOrchestra\BackOffice\Tests\Context;

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
    protected $siteRepository;

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
        $this->siteRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface');

        $this->contextManager = new ContextManager($this->session, $this->tokenStorage, $this->defaultLocale, $this->siteRepository);
    }

    /**
     * Test get default locale
     */
    public function testGetDefaultLocale()
    {
        $this->assertSame($this->defaultLocale, $this->contextManager->getDefaultLocale());
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
     * @param string $expectedLocale
     * @param string $userLocale
     * @param string $userClass
     *
     * @dataProvider provideExpectedLocaleUserLocaleAndUserClass
     */
    public function testGetCurrentLocaleWhenNotInSession($expectedLocale, $userLocale, $userClass)
    {
        $user = Phake::mock($userClass);
        Phake::when($user)->getLanguage()->thenReturn($userLocale);
        Phake::when($this->token)->getUser()->thenReturn($user);

        $this->assertSame($expectedLocale, $this->contextManager->getCurrentLocale());
    }

    /**
     * @return array
     */
    public function provideExpectedLocaleUserLocaleAndUserClass()
    {
        return array(
            array('fr', 'fr', 'OpenOrchestra\UserBundle\Model\UserInterface'),
            array('en', 'en', 'OpenOrchestra\UserBundle\Model\UserInterface'),
            array('en', 'fr', 'FOS\UserBundle\Model\UserInterface'),
            array('en', 'en', 'FOS\UserBundle\Model\UserInterface'),
        );
    }

    /**
     * Test get current local when user haven't language
     */
    public function testGetCurrentLocaleWhenUserNoLanguage()
    {
        $user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($user)->getLanguage()->thenReturn(null);
        Phake::when($this->token)->getUser()->thenReturn($user);

        $this->assertSame($this->defaultLocale, $this->contextManager->getCurrentLocale());
    }

    /**
     * Test getAvailableSites
     */
    public function testGetAvailableSites()
    {
        $site1 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site1)->getId()->thenReturn('id1');
        $group1 = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');
        Phake::when($group1)->getSite()->thenReturn($site1);
        $site2 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site2)->getId()->thenReturn('id2');
        $group2 = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');
        Phake::when($group2)->getSite()->thenReturn($site2);
        $group3 = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');
        Phake::when($group3)->getSite()->thenReturn($site2);
        $groups = array($group1, $group2, $group3);
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
     * Test with no site in group
     */
    public function testGetAvailableSitesIfNoSiteGroup()
    {
        $site1 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        $site2 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($this->siteRepository)->findByDeleted(false)->thenReturn(array($site1, $site2));

        $group = Phake::mock('OpenOrchestra\BackofficeBundle\Model\GroupInterface');
        Phake::when($group)->getSite()->thenReturn(null);
        $groups = array($group);

        $user = Phake::mock('OpenOrchestra\UserBundle\Document\User');
        Phake::when($user)->getGroups()->thenReturn($groups);

        Phake::when($this->token)->getUser()->thenReturn($user);

        $this->assertEquals(array($site1, $site2), $this->contextManager->getAvailableSites());
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
