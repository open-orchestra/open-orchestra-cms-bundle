<?php

namespace OpenOrchestra\BackofficeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class CheckAreaPresence
 */
class CheckAreaPresence extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.area.presence_required';

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
