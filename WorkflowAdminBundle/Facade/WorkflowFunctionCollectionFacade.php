<?php

namespace OpenOrchestra\WorkflowAdminBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class WorkflowFunctionCollectionFacade
 */
class WorkflowFunctionCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'workflow_functions';

    /**
     * @Serializer\Type("array<OpenOrchestra\WorkflowAdminBundle\Facade\WorkflowFunctionFacade>")
     */
    public $workflowFunctions = array();

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
     * @param FacadeInterface $workflowFunction
     */
    public function addWorkflowFunction(FacadeInterface $workflowFunction)
    {
        $this->workflowFunctions[] = $workflowFunction;
    }
}
