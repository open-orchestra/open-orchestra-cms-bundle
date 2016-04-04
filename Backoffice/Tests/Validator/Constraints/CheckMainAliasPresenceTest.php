<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\CheckMainAliasPresence;
use Symfony\Component\Validator\Constraint;

/**
 * Class CheckMainAliasPresenceTest
 */
class CheckMainAliasPresenceTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new CheckMainAliasPresence();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'OpenOrchestra\Backoffice\Validator\Constraints\CheckMainAliasPresenceValidator',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_backoffice_validators.website.exists_main_alias'
        );
    }
}
