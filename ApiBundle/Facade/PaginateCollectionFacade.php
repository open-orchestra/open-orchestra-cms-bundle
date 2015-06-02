<?php

namespace OpenOrchestra\ApiBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;

/**
 * Class PaginateCollectionFacade
 */
abstract class PaginateCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("recordsTotal")
     */
    public $recordsTotal;

    /**
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("recordsFiltered")
     */
    public $recordsFiltered;

    /**
     * @param $recordsTotal
     */
    public function setRecordsTotal($recordsTotal)
    {
        $this->recordsTotal = $recordsTotal;
    }

    /**
     * @param $recordsFiltered
     */
    public function setRecordsFiltered($recordsFiltered)
    {
        $this->recordsFiltered = $recordsFiltered;
    }
}
