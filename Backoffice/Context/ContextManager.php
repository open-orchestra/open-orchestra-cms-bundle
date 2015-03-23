<?php

namespace OpenOrchestra\Backoffice\Context;

use FOS\UserBundle\Model\GroupableInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Centralize app contextual datas
 */
class ContextManager implements CurrentSiteIdInterface
{
    const KEY_LOCALE = '_locale';
    const KEY_SITE = '_site';

    protected $session;
    protected $siteRepository;
    protected $tokenStorage;

    /**
     * Constructor
     *
     * @param Session                 $session
     * @param SiteRepositoryInterface $siteRepository
     * @param TokenStorageInterface   $tokenStorage
     * @param string                  $defaultLocale
     */
    public function __construct(Session $session, SiteRepositoryInterface $siteRepository, TokenStorageInterface $tokenStorage, $defaultLocale = 'en')
    {
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;

        if ($this->getCurrentLocale() == '') {
            $this->setCurrentLocale($defaultLocale);
        }

        $this->siteRepository = $siteRepository;
    }

    /**
     * Get current locale value
     *
     * @return string
     */
    public function getCurrentLocale()
    {
        return $this->session->get(self::KEY_LOCALE);
    }

    /**
     * Set current locale
     *
     * @param string $locale
     */
    public function setCurrentLocale($locale)
    {
        $this->session->set(self::KEY_LOCALE, $locale);
    }

    /**
     * Get availables sites on platform
     *
     * @return array<SiteInterface>
     */
    public function getAvailableSites()
    {
        $token = $this->tokenStorage->getToken();
        $sites = array();

        if ($token && ($user = $token->getUser()) instanceof GroupableInterface) {
            foreach ($user->getGroups() as $group) {
                $sites[] = $group->getSite();
            }
        }

        return $sites;
    }

    /**
     * Set current site
     *
     * @param string $siteId
     * @param string $siteName
     * @param string $siteDefaultLanguage
     */
    public function setCurrentSite($siteId, $siteName, $siteDefaultLanguage)
    {
        $this->session->set(
            self::KEY_SITE,
            array(
                'siteId' => $siteId,
                'name' => $siteName,
                'defaultLanguage' => $siteDefaultLanguage,
            )
        );
    }

    /**
     * Get the current site id
     *
     * @return string
     */
    public function getCurrentSiteId()
    {
        $site = $this->getCurrentSite();

        return $site['siteId'];
    }

    /**
     * Get the current domain
     *
     * @return string
     */
    public function getCurrentSiteName()
    {
        $site = $this->getCurrentSite();

        return $site['name'];
    }

    /**
     * Get the current default language of the current site
     *
     * @return string
     */
    public function getCurrentSiteDefaultLanguage()
    {
        $site = $this->getCurrentSite();

        return $site['defaultLanguage'];
    }

    /**
     * Get current selected site (BO Context)
     *
     * @return array
     */
    protected function getCurrentSite()
    {
        $currentSite = $this->session->get(self::KEY_SITE);

        if (!$currentSite || $currentSite['siteId'] == 0) {
            $sites = $this->getAvailableSites();

            $siteId = 0;
            $siteName = 'No site available';
            $locale = $this->getCurrentLocale();
            if (isset($sites[0])) {
                $siteId = $sites[0]->getSiteId();
                $siteName = $sites[0]->getName();
                $locale = $sites[0]->getDefaultLanguage();
            }
            $this->setCurrentSite($siteId, $siteName, $locale);
            $currentSite = $this->session->get(self::KEY_SITE);
        }

        return $currentSite;
    }
}
