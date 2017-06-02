<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\ContentTypeField;
use Symfony\Component\Validator\Constraint;

/**
 * Class ContentTypeFieldTest
 */
class ContentTypeFieldTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new ContentTypeField();
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'content_type_field',
            Constraint::CLASS_CONSTRAINT,
            'open_orchestra_backoffice_validators.content_type.disallowed_field_names'
        );
    }
}
