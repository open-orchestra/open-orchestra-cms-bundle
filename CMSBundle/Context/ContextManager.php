<?php

namespace PHPOrchestra\CMSBundle\Context;

use PHPOrchestra\ModelBundle\Model\SiteInterface;
use PHPOrchestra\ModelBundle\Repository\SiteRepository;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Centralize app contextual datas
 */
class ContextManager
{
    const KEY_LOCALE = '_locale';
    const KEY_SITE = '_site';

    protected $session;
    protected $siteRepository;

    /**
     * Constructor
     * 
     * @param Session        $session
     * @param SiteRepository $siteRepository
     * @param string         $defaultLocale
     */
    public function __construct(Session $session, SiteRepository $siteRepository, $defaultLocale = 'en')
    {
        $this->session = $session;
        if ($this->getCurrentLocale() == '') {
            $this->setCurrentLocale($defaultLocale);
        }
        
        $this->siteRepository = $siteRepository;
    }

    /**
     * Get current locale value
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
     */
    public function getAvailableSites()
    {
        $documentSites = $this->siteRepository->findAll();

        /** @var SiteInterface $site */
        return array_filter($documentSites, function($site) {
            return $site->getSiteId() != '' && $site->getDomain() != '';
        });
    }

    /**
     * Set current site
     * 
     * @param string $siteId
     * @param string $siteDomain
     */
    public function setCurrentSite($siteId, $siteDomain)
    {
        $this->session->set(
            self::KEY_SITE,
            array(
                'siteId' => $siteId,
                'domain' => $siteDomain
            )
        );
    }

    /**
     * Get current selected site (BO Context)
     *
     * @return array
     */
    public function getCurrentSite()
    {
        $currentSite = $this->session->get(self::KEY_SITE);

        if ($currentSite) {
            return $currentSite;
        } else {
            $sites = $this->getAvailableSites();

            if (isset($sites[0])) {
                $this->setCurrentSite($sites[0]->getSiteId(), $sites[0]->getDomain());
            } else {
                $this->setCurrentSite(0, 'No site available');
            }
        }

        return $this->session->get(self::KEY_SITE);
    }
}
