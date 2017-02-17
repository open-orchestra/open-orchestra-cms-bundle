<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\UniqueNodeSpecialPage;
use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueNodeSpecialPageTest
 */
class UniqueNodeSpecialPageTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new UniqueNodeSpecialPage();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'unique_node_special_page',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_backoffice_validators.node.unique_node_special_page'
        );
    }
}
