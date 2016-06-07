<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class BlockNodePattern
 */
class BooleanCondition extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.boolean_condition.pattern';

    /**
     * @return string|void
     */
    public function validatedBy()
    {
        return 'boolean_condition';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
