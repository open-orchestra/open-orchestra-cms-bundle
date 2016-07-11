<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

@trigger_error('The '.__NAMESPACE__.'\ChoiceArrayToStringTransformer class is deprecated since version 1.2.0 and will be removed in 2.0', E_USER_DEPRECATED);

/**
 * Class UniqueAreaId
 * @deprecated will be removed in 2.0
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
