<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueRedirection
 */
class UniqueRedirection extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.redirection.unique_pattern';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'unique_redirection';
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
