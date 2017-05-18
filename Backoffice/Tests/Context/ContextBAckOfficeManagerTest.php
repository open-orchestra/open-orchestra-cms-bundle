<?php

namespace OpenOrchestra\BackOffice\Tests\Context;

use OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface;
use OpenOrchestra\Backoffice\Context\ContextBackOfficeManager;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class ContextBackOfficeManagerTest
 */
class ContextBackOfficeManagerTest extends AbstractBaseTestCase
{
    /**
     * @var ContextBackOfficeManager
     */
    protected $contextManager;

    protected $token;
    protected $session;
    protected $tokenStorage;
    protected $defaultLocale;
    protected $siteRepository;
    protected $authorizationChecker;

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
        $this->authorizationChecker = Phake::mock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');

        $this->contextManager = new ContextBackOfficeManager($this->session, $this->tokenStorage, $this->defaultLocale, $this->siteRepository, $this->authorizationChecker);
    }

    /**
     * @param string $locale
     *
     * @dataProvider getLocale
     */
    public function testGetBackOfficeLanguage($locale)
    {
        Phake::when($this->session)->get(ContextBackOfficeInterface::KEY_LOCALE)->thenReturn($locale);

        $localReturned = $this->contextManager->getBackOfficeLanguage();

        $this->assertEquals($locale, $localReturned);
        Phake::verify($this->session, Phake::times(1))->get(ContextBackOfficeInterface::KEY_LOCALE);
    }

    /**
     * @param string $locale
     *
     * @dataProvider getLocale
     */
    public function testSetBackOfficeLanguage($locale)
    {
        $this->contextManager->setBackOfficeLanguage($locale);

        Phake::verify($this->session)->set(ContextBackOfficeInterface::KEY_LOCALE, $locale);
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
    public function testGetBackOfficeLanguageWhenNotInSession($expectedLocale, $userLocale, $userClass)
    {
        $user = Phake::mock($userClass);
        Phake::when($user)->getLanguage()->thenReturn($userLocale);
        Phake::when($this->token)->getUser()->thenReturn($user);

        $this->assertSame($expectedLocale, $this->contextManager->getBackOfficeLanguage());
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
    public function testBackOfficeLanguageWhenUserNoLanguage()
    {
        $user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($user)->getLanguage()->thenReturn(null);
        Phake::when($this->token)->getUser()->thenReturn($user);

        $this->assertSame($this->defaultLocale, $this->contextManager->getBackOfficeLanguage());
    }

    /**
     * Test getAvailableSites
     */
    public function testGetAvailableSites()
    {
        $site1 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site1)->getId()->thenReturn('id1');
        $group1 = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group1)->getSite()->thenReturn($site1);
        $site2 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site2)->getId()->thenReturn('id2');
        $group2 = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group2)->getSite()->thenReturn($site2);
        $group3 = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group3)->getSite()->thenReturn($site2);
        $groups = array($group1, $group2, $group3);
        $user = Phake::mock('OpenOrchestra\UserBundle\Document\User');
        Phake::when($user)->getGroups()->thenReturn($groups);
        Phake::when($this->token)->getRoles()->thenReturn(array());

        Phake::when($this->token)->getUser()->thenReturn($user);

        $this->assertEquals(array($site1, $site2), array_values($this->contextManager->getAvailableSites()));
    }

    /**
     * Test with no user
     */
    public function testGetAvailableSitesIfNoUser()
    {
        Phake::when($this->token)->getUser()->thenReturn(null);
        Phake::when($this->token)->getRoles()->thenReturn(array());

        $this->assertEmpty($this->contextManager->getAvailableSites());
    }

    /**
     * Test with no site in group
     */
    /**
     * Test with an user super admin
     */
    public function testGetAvailableSitesWithSuperAdmin()
    {
        $site1 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        $site2 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($this->siteRepository)->findByDeleted(false)->thenReturn(array($site1, $site2));
        Phake::when($this->authorizationChecker)->isGranted(ContributionRoleInterface::PLATFORM_ADMIN)->thenReturn(true);

        $this->assertEquals(array($site1, $site2), $this->contextManager->getAvailableSites());
    }


    /**
     * @param array $site
     *
     * @dataProvider getSite
     */
    public function testSetSite($site)
    {
        $this->contextManager->setSite($site['siteId'], $site['name'], $site['defaultLanguage'], $site['languages']);

        Phake::verify($this->session)->set(ContextBackOfficeInterface::KEY_SITE, array(
            'siteId' => $site['siteId'],
            'name' => $site['name'],
            'defaultLanguage' => $site['defaultLanguage'],
            'languages' => $site['languages'],
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
            array(array('siteId' => 'fakeId', 'name' => 'fakeName', 'defaultLanguage' => 'en', 'languages' => array('fr', 'en', 'de'))),
            array(array('siteId' => 'id', 'name' => 'name', 'defaultLanguage' => 'en', 'languages' => array('fr', 'en', 'de'))),
        );
    }

    /**
     * @param array  $site
     * @param string $siteId
     *
     * @dataProvider getSiteId
     */
    public function testGetSiteId($site, $siteId)
    {
        Phake::when($this->session)->get(Phake::anyParameters())->thenReturn($site);

        $this->assertEquals($siteId, $this->contextManager->getSiteId());

        Phake::verify($this->session, Phake::times(1))->get(ContextBackOfficeInterface::KEY_SITE);
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
    public function testGetSiteName($site, $domain)
    {
        Phake::when($this->session)->get(Phake::anyParameters())->thenReturn($site);

        $this->assertEquals($domain, $this->contextManager->getSiteName());

        Phake::verify($this->session, Phake::times(1))->get(ContextBackOfficeInterface::KEY_SITE);
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
    public function testGetSiteDefaultLanguage($site, $domain)
    {
        Phake::when($this->session)->get(Phake::anyParameters())->thenReturn($site);

        $this->assertEquals($domain, $this->contextManager->getSiteDefaultLanguage());

        Phake::verify($this->session, Phake::times(1))->get(ContextBackOfficeInterface::KEY_SITE);
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

    /**
     * @param $site  $site
     * @param array  $languages
     *
     * @dataProvider siteLanguagesProvider
     */
    public function testGetSiteLanguages(array $site, array $languages)
    {
        Phake::when($this->session)->get(Phake::anyParameters())->thenReturn($site);

        $this->assertEquals($languages, $this->contextManager->getSiteLanguages());

        Phake::verify($this->session, Phake::times(1))->get(ContextBackOfficeInterface::KEY_SITE);
    }


    /**
     * SiteId provider languages
     *
     * @return array
     */
    public function siteLanguagesProvider()
    {
        return array(
            array(array('siteId' => 'fakeId', 'name' => 'fakeName', 'defaultLanguage' => 'en', 'languages' => array('en')), array('en')),
            array(array('siteId' => 'id', 'name' => 'name', 'defaultLanguage' => 'fr', 'languages' => array('en', 'de')), array('en', 'de')),
        );
    }


    /**
     * @param string|null $expectedLocale
     * @param string      $userLocale
     * @param string      $userClass
     *
     * @dataProvider provideCurrentSiteDefaultLanguage
     */
    public function testGetSiteContributionLanguage($expectedLocale, $userLocale, $userClass)
    {

        Phake::when($this->session)->get(Phake::anyParameters())->thenReturn(array('siteId' => 'fakeId', 'name' => 'fakeName', 'defaultLanguage' => 'en', 'languages' => array('en')));


        $user = Phake::mock($userClass);
        Phake::when($user)->hasLanguageBySite(Phake::anyParameters())->thenReturn(true);
        Phake::when($user)->getLanguageBySite(Phake::anyParameters())->thenReturn($userLocale);
        Phake::when($this->token)->getUser()->thenReturn($user);

        $this->assertSame($expectedLocale, $this->contextManager->getSiteContributionLanguage());
    }

    /**
     * @return array
     */
    public function provideCurrentSiteDefaultLanguage()
    {
        return array(
            array('fr', 'fr', 'OpenOrchestra\UserBundle\Model\UserInterface'),
            array('en', 'en', 'OpenOrchestra\UserBundle\Model\UserInterface'),
            array('en', 'fr', 'FOS\UserBundle\Model\UserInterface'),
            array('en', 'en', 'FOS\UserBundle\Model\UserInterface'),
        );
    }

    /**
     * Test clear context
     */
    public function testClearContext()
    {
        $this->contextManager->clearContext();
        Phake::verify($this->session)->remove(ContextBackOfficeInterface::KEY_SITE);
        Phake::verify($this->session)->remove(ContextBackOfficeInterface::KEY_LOCALE);
        Phake::verify($this->token)->setAuthenticated(false);
    }
}
