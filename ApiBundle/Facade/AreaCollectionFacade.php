<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class AreaCollectionFacade
 */
class AreaCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'areas';

    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\AreaFacade>")
     */
    protected $areas = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addArea(FacadeInterface $facade)
    {
        $this->areas[] = $facade;
    }

    /**
     * return array
     */
    public function getAreas()
    {
        return $this->areas;
    }
}
