<?php

namespace OpenOrchestra\WorkflowAdminBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\ApiBundle\Facade\PaginateCollectionFacade;

/**
 * Class WorkflowTransitionFacade
 */
class WorkflowTransitionFacade extends PaginateCollectionFacade
{
    /**
     * @Serializer\Type("OpenOrchestra\WorkflowAdminBundle\Facade\StatusFacade")
     */
    public $statusFrom;

    /**
     * @Serializer\Type("OpenOrchestra\WorkflowAdminBundle\Facade\StatusFacade")
     */
    public $statusTo;
}
