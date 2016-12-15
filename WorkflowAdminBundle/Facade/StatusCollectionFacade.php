<?php

namespace OpenOrchestra\WorkflowAdminBundle\Facade;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\ApiBundle\Facade\PaginateCollectionFacade;

/**
 * Class StatusCollectionFacade
 */
class StatusCollectionFacade extends PaginateCollectionFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'statuses';

    /**
     * @Serializer\Type("array<OpenOrchestra\WorkflowAdminBundle\Facade\StatusFacade>")
     */
    protected $statuses = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addStatus(FacadeInterface $facade)
    {
        $this->statuses[] = $facade;
    }
}
