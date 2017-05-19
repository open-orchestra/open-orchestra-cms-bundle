<?php

namespace OpenOrchestra\Backoffice\Context;

use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;

/**
 * Centralize app contextual datas
 *
 * @deprecated use OpenOrchestra\Backoffice\Context\ContextBackOfficeManager
 */
class ContextManager extends ContextBackOfficeManager implements CurrentSiteIdInterface
{
    /**
     * Get current locale value
     * @deprecated use OpenOrchestra\Backoffice\Context\ContextBackOfficeManager::getBackOfficeLanguage
     * @return string
     */
    public function getCurrentLocale()
    {
        return parent::getBackOfficeLanguage();
    }

    /**
     * Set current locale
     * @deprecated use OpenOrchestra\Backoffice\Context\ContextBackOfficeManager::setBackOfficeLanguage
     * @param string $locale
     */
    public function setCurrentLocale($locale)
    {
        parent::setBackOfficeLanguage($locale);
    }

    /**
     * Get default locale
     * @deprecated
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * Set current site
     *
     * @param string $siteId
     * @param string $siteName
     * @param string $siteDefaultLanguage
     * @param array $languages
     * @deprecated use OpenOrchestra\Backoffice\Context\ContextBackOfficeManager::setSite
     */
    public function setCurrentSite($siteId, $siteName, $siteDefaultLanguage, array $languages)
    {
        parent::setSite($siteId, $siteName, $siteDefaultLanguage, $languages);
    }

    /**
     * Get the current site id
     *
     * @deprecated use OpenOrchestra\Backoffice\Context\ContextBackOfficeManager::getSiteId
     */
    public function getCurrentSiteId()
    {
        return parent::getSiteId();
    }

    /**
     * Get the current domain
     * @deprecated use OpenOrchestra\Backoffice\Context\ContextBackOfficeManager::getSiteName
     * @return string
     */
    public function getCurrentSiteName()
    {
        return parent::getSiteName();
    }

    /**
     * Get the current default language of the current site
     * @deprecated use OpenOrchestra\Backoffice\Context\ContextBackOfficeManager::getSiteDefaultLanguage
     * @return string
     */
    public function getCurrentSiteDefaultLanguage()
    {
        return parent::getSiteDefaultLanguage();
    }

    /**
     * Get the current default language setted by the user for the current site
     * @deprecated use OpenOrchestra\Backoffice\Context\ContextBackOfficeManager::getSiteContributionLanguage
     * @return string
     */
    public function getUserCurrentSiteDefaultLanguage()
    {
        return parent::getSiteContributionLanguage();
    }


    /**
     * Get languages of the current site
     * @deprecated use OpenOrchestra\Backoffice\Context\ContextBackOfficeManager::getSiteLanguages
     * @return array
     */
    public function getCurrentSiteLanguages()
    {
        return parent::getSiteLanguages();
    }
}
