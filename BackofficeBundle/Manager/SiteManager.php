<?php

namespace OpenOrchestra\BackofficeBundle\Manager;

use OpenOrchestra\ModelInterface\Model\SiteInterface;

/**
 * Class NodeManager
 */
class SiteManager
{
    protected $siteClass;

    /**
     * Constructor
     *
     * @param string $siteClass
     */
    public function __construct($siteClass)
    {
       $this->siteClass = $siteClass;
    }

    /**
     * @return SiteInterface
     */
    public function initializeNewSite()
    {
        $site = new $this->siteClass();
        $site->setSitemapPriority(SiteInterface::PRIORITY_DEFAULT);
        $site->setSitemapChangefreq(SiteInterface::CHANGE_FREQ_DEFAULT);
        $site->setMetaIndex(true);
        $site->setMetaFollow(true);
        
        return $site;
    }
}
