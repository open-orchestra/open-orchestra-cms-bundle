<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueMainAlias
 */
class UniqueMainAlias extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.website.unique_main_alias';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'unique_main_alias';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
