<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class AreaRowLayout
 */
class AreaRowLayout extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.area.row_layout';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'area_row_layout';
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
