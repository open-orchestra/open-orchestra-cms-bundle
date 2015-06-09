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
    public $collectionName = 'sites';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\SiteFacade>")
     */
    protected $sites = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addSite(FacadeInterface $facade)
    {
        $this->sites[] = $facade;
    }
}
