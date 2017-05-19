<?php

namespace OpenOrchestra\Backoffice\Context;


/**
 * Interface ContextBackOfficeInterface
 */
interface ContextBackOfficeInterface
{
    const KEY_LOCALE = '_locale';
    const KEY_SITE = '_site';

    /**
     * Get current back office language
     *
     * @return string
     */
    public function getBackOfficeLanguage();

    /**
     * Get availables sites on platform
     *
     * @return array<SiteInterface>
     */
    public function getAvailableSites();

    /**
     * Get the current site id
     *
     * @return string
     */
    public function getSiteId();

    /**
     * Get the current domain
     *
     * @return string
     */
    public function getSiteName();

    /**
     * Get the default language of the current site
     *
     * @return string
     */
    public function getSiteDefaultLanguage();

    /**
     * Get languages of the current site
     *
     * @return array
     */
    public function getSiteLanguages();

    /**
     * Get the  current default language setted by the user for the current site
     *
     * @return string
     */
    public function getSiteContributionLanguage();

    /**
     * Clear saved context
     */
    public function clearContext();

    /**
     * Set current back office language
     *
     * @param string $language
     */
    public function setBackOfficeLanguage($language);

    /**
     * Set current site
     *
     * @param string $siteId
     * @param string $siteName
     * @param string $siteDefaultLanguage
     * @param array $languages
     */
    public function setSite($siteId, $siteName, $siteDefaultLanguage, array $languages);
}
