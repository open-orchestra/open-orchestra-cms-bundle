<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use OpenOrchestra\Backoffice\Validator\Constraints\UnremovableLanguageCondition;
use Phake;

/**
 * Test UnremovableLanguageConditionTest
 */
class UnremovableLanguageConditionTest extends AbstractConstraintTest
{
    /**
     * Set up the test
     */
    public function setUp()
    {
        $site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($site)->getLanguages()->thenReturn(array());
        $this->constraint = new UnremovableLanguageCondition($site);
    }

    /**
     * Test Constraint
     */
    public function testConstraint()
    {
        $this->assertConstraint(
            $this->constraint,
            'unremovable_language',
            Constraint::PROPERTY_CONSTRAINT,
            'open_orchestra_backoffice_validators.website.unremovable_language'
        );
    }
}
