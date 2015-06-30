<?php

namespace OpenOrchestra\BackofficeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class ContentTemplate
 */
class ContentTemplate extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.form.content.content_template';

    /**
     * @return string|void
     */
    public function validatedBy()
    {
        return 'content_template';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
