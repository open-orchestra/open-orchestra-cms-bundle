<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\BooleanCondition;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Symfony\Component\Validator\Constraint;

/**
 * Class BooleanConditionTest
 */
class BooleanConditionTest extends AbstractBaseTestCase
{
    /**
     * @var BooleanCondition
     */
    protected $constraint;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new BooleanCondition();
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Validator\Constraint', $this->constraint);
    }

    /**
     * test target
     */
    public function testTarget()
    {
        $this->assertSame(Constraint::PROPERTY_CONSTRAINT, $this->constraint->getTargets());
    }

    /**
     * test message
     */
    public function testMessages()
    {
        $this->assertSame('open_orchestra_backoffice_validators.boolean_condition.pattern', $this->constraint->message);
    }

    /**
     * Test validate by
     */
    public function testValidateBy()
    {
        $this->assertSame('boolean_condition', $this->constraint->validatedBy());
    }
}
