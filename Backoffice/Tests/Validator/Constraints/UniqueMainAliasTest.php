<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\UniqueMainAlias;
use Symfony\Component\Validator\Constraint;

/**
 * Test UniqueMainAliasTest
 */
class UniqueMainAliasTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new UniqueMainAlias();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'unique_main_alias',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_backoffice_validators.website.unique_main_alias'
        );
    }
}
