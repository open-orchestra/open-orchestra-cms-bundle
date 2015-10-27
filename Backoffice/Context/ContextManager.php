<?php

namespace OpenOrchestra\Backoffice\Context;

use FOS\UserBundle\Model\GroupableInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Centralize app contextual datas
 */
class ContextManager implements CurrentSiteIdInterface
{
    const KEY_LOCALE = '_locale';
    const KEY_SITE = '_site';

    protected $siteId;
    protected $session;
    protected $tokenStorage;
    protected $currentLanguage;
    protected $defaultLocale;
    protected $siteRepository;

    /**
     * Constructor
     *
     * @param Session                 $session
     * @param TokenStorageInterface   $tokenStorage
     * @param string                  $defaultLocale
     * @param SiteRepositoryInterface $siteRepository
     */
    public function __construct(
        Session $session,
        TokenStorageInterface $tokenStorage,
        $defaultLocale,
        SiteRepositoryInterface $siteRepository
    )
    {
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->defaultLocale = $defaultLocale;
        $this->siteRepository = $siteRepository;
    }

    /**
     * Get current locale value
     *
     * @return string
     */
    public function getCurrentLocale()
    {
        $currentLanguage = $this->session->get(self::KEY_LOCALE);

        if (!$currentLanguage) {
            $currentLanguage = $this->getDefaultLocale();
            $token = $this->tokenStorage->getToken();
            if ($token && ($user = $token->getUser()) instanceof UserInterface) {
                $currentLanguage = $user->getLanguage();
                $this->setCurrentLocale($currentLanguage);
            }
        }

        return $currentLanguage;

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
     * Get default locale
     *
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
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
                /** @var SiteInterface $site */
                $site = $group->getSite();
                if ($site === null) {
                    return $this->siteRepository->findByDeleted(false);
                } else {
                    if (!$site->isDeleted()) {
                        $sites[] = $site;
                    }
                }
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
        $this->siteId = $siteId;
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
        if (is_null($this->siteId)) {
            $this->siteId = $this->getCurrentSite()['siteId'];
        }

        return $this->siteId;
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
        if (is_null($this->currentLanguage)) {
            $this->currentLanguage = $this->getCurrentSite()['defaultLanguage'];
        }

        return $this->currentLanguage;
    }

    /**
     * Get current selected site (BO Context)
     *
     * @return array
     */
    protected function getCurrentSite()
    {
        $currentSite = $this->session->get(self::KEY_SITE);

        if (!$currentSite || (is_integer($currentSite['siteId']) && $currentSite['siteId'] == 0)) {
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
