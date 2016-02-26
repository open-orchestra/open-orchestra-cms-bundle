<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class DatatableEntityParameterFacade
 */
class DatatableEntityParameterFacade implements FacadeInterface
{
    /**
     * @Serializer\Type("array<OpenOrchestra\ApiBundle\Facade\DatatableColumnParameterFacade>")
     */
    protected $columnParameter = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addColumnParameter(FacadeInterface $facade)
    {
        $this->columnParameter[] = $facade;
    }
}
