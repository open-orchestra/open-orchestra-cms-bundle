<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Facade;

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
     * @Serializer\Type("array<OpenOrchestra\WorkflowFunctionAdminBundle\Facade\WorkflowFunctionFacade>")
     */
    public $workflowFunctions = array();

    /**
     * @param FacadeInterface $log
     */
    public function addWorkflowFunction(FacadeInterface $workflowFunction)
    {
        $this->workflowFunctions[] = $workflowFunction;
    }

    /**
     * @return mixed
     */
    public function getWorkflowFunctions()
    {
        return $this->workflowFunctions;
    }
}
