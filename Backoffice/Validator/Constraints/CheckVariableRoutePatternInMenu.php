<?php

namespace OpenOrchestra\Backoffice\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class CheckVariableRoutePatternInMenu
 */
class CheckVariableRoutePatternInMenu extends Constraint
{
    public $message = 'open_orchestra_backoffice_validators.node.check_variable_route_pattern_in_menu';

    /**
     * @return string|void
     */
    public function validatedBy()
    {
        return 'check_variable_route_pattern_in_menu';
    }

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
