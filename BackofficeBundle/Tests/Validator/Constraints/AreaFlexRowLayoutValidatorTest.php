<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Validator\Constraints;

use OpenOrchestra\BackofficeBundle\Validator\Constraints\AreaFlexRowLayoutValidator;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class AreaFlexRowLayoutValidatorTest
 */
class AreaFlexRowLayoutValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var AreaFlexRowLayoutValidator
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

        $this->validator = new AreaFlexRowLayoutValidator();
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
     * @param string $columnLayout
     * @param int    $violationTimes
     *
     * @dataProvider provideCountAndViolation
     */
    public function testValidate($columnLayout, $violationTimes)
    {
        $this->validator->validate($columnLayout, $this->constraint);
        Phake::verify($this->context, Phake::times($violationTimes))->buildViolation(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideCountAndViolation()
    {
        return array(
            array('10%', 0),
            array('100px', 0),
            array('1/3', 1),
            array('auto', 0),
            array('10%,1/5', 1),
            array('*', 1),
            array('10%px', 1),
            array('1/3px', 1),
            array('10%, 100px, e', 1),
            array('', 1),
        );
    }
}
