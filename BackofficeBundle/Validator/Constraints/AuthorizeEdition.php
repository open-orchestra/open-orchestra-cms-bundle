<?php

namespace OpenOrchestra\BackofficeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class AuthorizeEdition
 */
class AuthorizeEdition extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.authorize.edition';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'authorize_edition';
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
