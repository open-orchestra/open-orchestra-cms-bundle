<?php

namespace OpenOrchestra\Workflow\Event;

use OpenOrchestra\ModelInterface\Model\WorkflowRightInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class WorkflowRightEvent
 */
class WorkflowRightEvent extends Event
{
    protected $workflowRight;

    /**
     * @param WorkflowRightInterface $workflowRight
     */
    public function __construct(WorkflowRightInterface $workflowRight)
    {
        $this->workflowRight = $workflowRight;
    }

    /**
     * @return WorkflowRightInterface
     */
    public function getWorkflowRight()
    {
        return $this->workflowRight;
    }
}
