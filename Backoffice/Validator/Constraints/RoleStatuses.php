<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class RoleStatuses
 */
class RoleStatuses extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.role.duplicate_statuses';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'role_statuses';
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
