<?php

namespace OpenOrchestra\Workflow\Model;

/**
 * Interface WorkflowProfileInterface
 */
interface WorkflowProfileInterface
{
    /**
     * @param WorkflowTransitionInterface $transition
     */
    public function addTransition(WorkflowTransitionInterface $transition);
}
