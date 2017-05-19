<?php

namespace OpenOrchestra\Backoffice\Context;

/**
 * Class TestContextManager
 */
class TestContextManager extends ContextBackOfficeManager
{
    protected $siteId = '2';
    protected $defaultLanguage = 'fr';
    protected $siteName = 'Demo site';
    protected $languages = array('fr', 'en', 'de');
    protected $defaultLocale;

    /**
     * @return string
     */
    public function getBackOfficeLanguage()
    {
        return $this->defaultLocale;
    }

    /**
     * @param string $locale
     */
    public function setBackOfficeLanguage($locale)
    {
        $this->defaultLocale = $locale;
    }

    /**
     * @param string $siteId
     * @param string $siteDomain
     * @param string $defaultLanguage
     * @param array  $languages
     */
    public function setSite($siteId, $siteDomain, $defaultLanguage, array $languages)
    {
        $this->siteId = $siteId;
        $this->siteDomain = $siteDomain;
        $this->defaultLanguage = $defaultLanguage;
        $this->languages = $languages;
    }

    /**
     * @return string
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @return string
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * @return string
     */
    public function getSiteDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * @return string
     */
    public function getSiteContributionLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * @return array
     */
    public function getAvailableSites()
    {
        return $this->siteRepository->findByDeleted(false);
    }

    /**
     * @return array
     */
    public function getSiteLanguages()
    {
        return $this->languages;
    }
}
