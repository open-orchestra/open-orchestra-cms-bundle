<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueAreaId
 */
class UniqueAreaId extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.area.unique_area_id';

    /**
     * @return string|void
     */
    public function validatedBy()
    {
        return 'unique_area_id';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
