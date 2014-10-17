<?php

namespace PHPOrchestra\Backoffice\Context;

use PHPOrchestra\ModelBundle\Repository\SiteRepository;

/**
 * Class TestContextManager
 */
class TestContextManager extends ContextManager
{
    protected $defaultLocale;
    protected $siteId = '1';
    protected $siteDomain = 'www.aphpOrchestra.fr';

    /**
     * @param SiteRepository $siteRepository
     * @param string         $defaultLocale
     */
    public function __construct(SiteRepository $siteRepository, $defaultLocale = 'en')
    {
        $this->siteRepository = $siteRepository;
        $this->defaultLocal = $defaultLocale;
    }

    /**
     * @return string
     */
    public function getCurrentLocale()
    {
        return $this->defaultLocal;
    }

    /**
     * @param string $locale
     */
    public function setCurrentLocale($locale)
    {
        $this->defaultLocal = $locale;
    }

    /**
     * @param string $siteId
     * @param string $siteDomain
     */
    public function setCurrentSite($siteId, $siteDomain)
    {
        $this->siteId = $siteId;
        $this->siteDomain = $siteDomain;
    }

    /**
     * @return string
     */
    public function getCurrentSiteId()
    {
        return $this->siteId;
    }

    /**
     * @return string
     */
    public function getCurrentSiteDomain()
    {
        return $this->siteDomain;
    }
}
