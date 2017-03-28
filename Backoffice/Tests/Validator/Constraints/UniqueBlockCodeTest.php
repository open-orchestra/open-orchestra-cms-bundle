<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\UniqueBlockCode;
use Symfony\Component\Validator\Constraint;

/**
 * Test UniqueBlockCodeTest
 */
class UniqueBlockCodeTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new UniqueBlockCode();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'unique_block_code',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_backoffice_validators.block.unique_code'
        );
    }
}
