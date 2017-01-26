<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use OpenOrchestra\Backoffice\Validator\Constraints\UniqueRedirection;

/**
 * Test UniqueRedirectionTest
 */
class UniqueRedirectionTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new UniqueRedirection();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'unique_redirection',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_backoffice_validators.redirection.unique_pattern'
        );
    }
}
