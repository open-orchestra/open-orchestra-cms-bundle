<?php

namespace OpenOrchestra\ApiBundle\Facade;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class SiteCollection
 */
class SiteCollectionFacade extends PaginateCollectionFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'siteAliases';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\SiteFacade>")
     */
    protected $sites = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addSiteAlias(FacadeInterface $facade)
    {
        $this->siteAliases[] = $facade;
    }

    /**
     * @return array
     */
    public function getSiteAliases()
    {
        return $this->siteAliases;
    }
}
