<?php

namespace PHPOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class SiteCollection
 */
class SiteCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'sites';

    /**
     * @Serializer\Type("array<PHPOrchestra\ApiBundle\Facade\SiteFacade>")
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
