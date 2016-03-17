<?php

namespace OpenOrchestra\Backoffice\Manager;

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
     * @param string $siteAliasClass
     */
    public function __construct($siteClass, $siteAliasClass)
    {
       $this->siteClass = $siteClass;
       $this->siteAliasClass = $siteAliasClass;
    }

    /**
     * @return SiteInterface
     */
    public function initializeNewSite()
    {
        $siteAliasClass = $this->siteAliasClass;
        $siteAlias = new $siteAliasClass();

        $site = new $this->siteClass();
        $site->setSitemapPriority(SiteInterface::PRIORITY_DEFAULT);
        $site->setSitemapChangefreq(SiteInterface::CHANGE_FREQ_DEFAULT);
        $site->setMetaIndex(true);
        $site->setMetaFollow(true);
        $site->addAlias($siteAlias);

        return $site;
    }
}
