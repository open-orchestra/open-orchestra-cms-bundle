<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class DatatableColumnParameterFacade
 */
class DatatableColumnParameterFacade implements FacadeInterface
{
    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("string")
     */
    public $title;

    /**
     * @Serializer\Type("boolean")
     * @Serializer\SerializedName("activateColvis")
     */
    public $activateColvis;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("searchField")
     */
    public $searchField;

    /**
     * @Serializer\Type("boolean")
     */
    public $visible;

    /**
     * @Serializer\Type("boolean")
     */
    public $orderable;

    /**
     * @Serializer\Type("boolean")
     */
    public $searchable;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("orderDirection")
     */
    public $orderDirection;
}
