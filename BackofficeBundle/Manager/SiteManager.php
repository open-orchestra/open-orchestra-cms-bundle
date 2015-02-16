<?php

namespace PHPOrchestra\BackofficeBundle\Manager;

use PHPOrchestra\ModelInterface\Model\SiteInterface;

/**
 * Class NodeManager
 */
class SiteManager
{
    protected $siteClass;

    /**
     * Constructor
     *
     * @param string                   $siteClass
     */
    public function __construct($siteClass)
    {
       $this->siteClass = $siteClass;
    }

    public function initializeNewSite()
    {
        $site = new $this->siteClass();
        $site->setSitemapPriority(SiteInterface::PRIORITY_DEFAULT);
        $site->setSitemapChangefreq(SiteInterface::CHANGE_FREQ_DEFAULT);

        return $site;
    }
}
