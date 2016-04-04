<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class CheckMainAliasPresence
 */
class CheckMainAliasPresence extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.website.exists_main_alias';

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
