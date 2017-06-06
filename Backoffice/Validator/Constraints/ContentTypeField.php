<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class ContentTypeField
 */
class ContentTypeField extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.content_type.disallowed_field_names';

    /**
     * @return string|void
     */
    public function validatedBy()
    {
        return 'content_type_field';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
