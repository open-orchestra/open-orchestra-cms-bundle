<?php

namespace OpenOrchestra\Workflow\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class WorkflowParameter
 */
class WorkflowParameter extends Constraint
{
    public $requiredParameterMessage = 'open_orchestra_workflow_admin_validators.workflow_parameters.required';
    public $uniqueParameterMessage   = 'open_orchestra_workflow_admin_validators.workflow_parameters.unique';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'workflow_parameters';
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
