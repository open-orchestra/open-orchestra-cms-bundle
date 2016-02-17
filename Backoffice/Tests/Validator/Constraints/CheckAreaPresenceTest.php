<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\CheckAreaPresence;
use Symfony\Component\Validator\Constraint;

/**
 * Class CheckAreaPresenceTest
 */
class CheckAreaPresenceTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new CheckAreaPresence();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'OpenOrchestra\Backoffice\Validator\Constraints\CheckAreaPresenceValidator',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_backoffice_validators.area.presence_required'
        );
    }
}
