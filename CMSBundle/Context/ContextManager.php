<?php
/**
 * This file is part of the PHPOrchestra\CMSBundle.
 *
 * @author Noël Gilain <noel.gilain@businessdecision.com>
 */

namespace PHPOrchestra\CMSBundle\Context;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Centralize app contextual datas
 * 
 * @author Noël GILAIN <noel.gilain@businessdecision.com>
 */
class ContextManager
{
    const KEY_LOCALE = '_locale';
    const KEY_SITE = '_site';

    private $session = null;
    private $documentManager = null;

    /**
     * Constructor
     * 
     * @param object $session
     * @param object $documentManager
     * @param string $defaultLocale
     */
    public function __construct($session, $documentManager, $defaultLocale = 'en')
    {
        $this->session = $session;
        if ($this->getCurrentLocale() == '') {
            $this->setCurrentLocale($defaultLocale);
        }
        
        $this->documentManager = $documentManager;
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
        $documentSites = $this->documentManager->getDocuments('Site');
        $sites = array();
        
        foreach ($documentSites as $site) {
            $mongoId = (string) $site->getId();
            $domain = $site->getDomain();
            if ($mongoId != '' && $domain != '') {
                $sites[] = array(
                    'id' => $mongoId,
                    'domain' => $domain
                );
            }
        }
        
        return $sites;
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
                'id' => $siteId,
                'domain' => $siteDomain
            )
        );
    }

    /**
     * Get current selected site (BO Context)
     */
    public function getCurrentSite()
    {
        $currentSite = $this->session->get(self::KEY_SITE);
        
        if (!isset($currentSite)) {
            $sites = $this->getAvailableSites();
            
            if (isset($sites[0]) && isset($sites[0]['id']) && isset($sites[0]['domain'])) {
                $this->setCurrentSite($sites[0]['id'], $sites[0]['domain']);
            } else {
                $this->setCurrentSite(0, 'No site available');
            }
        }
        
        return $this->session->get(self::KEY_SITE);
    }
}
