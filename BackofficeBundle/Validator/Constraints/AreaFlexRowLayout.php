<?php

namespace OpenOrchestra\BackofficeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class AreaFlexRowLayout
 */
class AreaFlexRowLayout extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.form.area_flex.row_layout';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'area_flex_row_layout';
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
