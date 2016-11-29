<?php

namespace OpenOrchestra\Workflow\Event;

use OpenOrchestra\Workflow\Model\WorkflowFunctionInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class WorkflowFunctionEvent
 */
class WorkflowFunctionEvent extends Event
{
    protected $workflowFunction;

    /**
     * @param WorkflowFunctionInterface $workflowFunction
     */
    public function __construct(WorkflowFunctionInterface $workflowFunction)
    {
        $this->workflowFunction = $workflowFunction;
    }

    /**
     * @return WorkflowFunctionInterface
     */
    public function getWorkflowFunction()
    {
        return $this->workflowFunction;
    }
}
