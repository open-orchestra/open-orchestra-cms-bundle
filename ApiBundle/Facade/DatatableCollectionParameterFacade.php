<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class DatatableCollectionParameterFacade
 */
class DatatableCollectionParameterFacade implements FacadeInterface
{
    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\DatatableEntityParameterFacade>")
     */
    protected $entityParameter = array();

    /**
     * @param string          $name
     * @param FacadeInterface $facade
     */
    public function addEntityParameter($name, FacadeInterface $facade)
    {
        $this->entityParameter[$name] = $facade;
    }
}
