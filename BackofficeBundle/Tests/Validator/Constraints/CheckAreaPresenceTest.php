<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Validator\Constraints;

use OpenOrchestra\BackofficeBundle\Validator\Constraints\CheckAreaPresence;
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
            'OpenOrchestra\BackofficeBundle\Validator\Constraints\CheckAreaPresenceValidator',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_backoffice_validators.area.presence_required'
        );
    }
}
