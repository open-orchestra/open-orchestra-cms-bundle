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
     */
    public $recordsTotal;

    /**
     * @Serializer\Type("integer")
     */
    public $recordFiltered;

    /**
     * @param $recordsTotal
     */
    public function setRecordsTotal($recordsTotal)
    {
        $this->recordsTotal = $recordsTotal;
    }

    /**
     * @param $recordFiltered
     */
    public function setRecordFiltered($recordFiltered)
    {
        $this->recordFiltered = $recordFiltered;
    }
}
