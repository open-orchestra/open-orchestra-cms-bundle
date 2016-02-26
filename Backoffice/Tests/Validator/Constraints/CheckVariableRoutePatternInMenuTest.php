<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\CheckVariableRoutePatternInMenu;
use Symfony\Component\Validator\Constraint;

/**
 * Class CheckVariableRoutePatternInMenuTest
 */
class CheckVariableRoutePatternInMenuTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new CheckVariableRoutePatternInMenu();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'check_variable_route_pattern_in_menu',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_backoffice_validators.node.check_variable_route_pattern_in_menu'
        );
    }
}
