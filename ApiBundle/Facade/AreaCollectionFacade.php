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
     * @Serializer\Type("array<string,OpenOrchestra\ApiBundle\Facade\AreaFacade>")
     */
    protected $areas = array();

    /**
     * @param FacadeInterface $facade
     * @param string          $key
     */
    public function setArea(FacadeInterface $facade, $key)
    {
        $this->areas[$key] = $facade;
    }

    /**
     * return array
     */
    public function getAreas()
    {
        return $this->areas;
    }
}
