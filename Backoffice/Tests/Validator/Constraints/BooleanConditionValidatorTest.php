<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\BooleanConditionValidator;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class BooleanConditionValidatorTest
 */
class BooleanConditionValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var BooleanConditionValidator
     */
    protected $validator;
    protected $context;
    protected $constraint;
    protected $constraintViolationBuilder;


    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = Phake::mock('Symfony\Component\Validator\Constraint');
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');

        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->validator = new BooleanConditionValidator();
        $this->validator->initialize($this->context);
    }

    /**
     * Test instance
     */
    public function testClass()
    {
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $this->validator);
    }

    /**
     * @param string $condition
     * @param int    $violationTimes
     *
     * @dataProvider provideCountAndViolation
     */
    public function testValidate($condition, $violationTimes)
    {
        $this->validator->validate($condition, $this->constraint);
        Phake::verify($this->context, Phake::times($violationTimes))->buildViolation(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideCountAndViolation()
    {
        return array(
            array('( NOT ( cat:X1 OR cat:X2 ) AND author:AAA ) OR ( T1 OR T2 OR NOT T3 )', 0),
            array('( NOT NOT ( cat:X1 OR cat:X2 ) AND author:AAA ) OR ( T1 OR T2 OR NOT T3 )', 1),
            array('( NOT ( cat:X1 OR cat:X2 ) AND author:AAA ) OR ( T1 OR T2 AND NOT T3 )', 1),
            array('( NOT (cat:X1 OR cat:X2 ) AND author:AAA ) OR ( T1 OR T2 OR NOT T3 )', 1),
            array(' NOT ( cat:X1 OR cat:X2 ) AND author:AAA ) OR ( T1 OR T2 OR NOT T3 )', 1),
        );
    }
}
