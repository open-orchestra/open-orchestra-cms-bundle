<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Validator\Constraints;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\BackofficeBundle\Validator\Constraints\CheckAreaPresenceValidator;

/**
 * Class CheckAreaPresenceValidatorTest
 */
class CheckAreaPresenceValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var CheckAreaPresenceValidator
     */
    protected $validator;

    protected $node;
    protected $areas;
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
        Phake::when($this->constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->areas = Phake::mock('Doctrine\Common\Collections\ArrayCollection');

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node)->getAreas()->thenReturn($this->areas);

        $this->validator = new CheckAreaPresenceValidator();
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
     * @param int $count
     * @param int $violationTimes
     *
     * @dataProvider provideCountAndViolation
     */
    public function testAddViolationOrNot($count, $violationTimes)
    {
        Phake::when($this->areas)->count()->thenReturn($count);

        $this->validator->validate($this->node, $this->constraint);

        Phake::verify($this->constraintViolationBuilder, Phake::times($violationTimes))->atPath('nodeSource');
        Phake::verify($this->constraintViolationBuilder, Phake::times($violationTimes))->atPath('templateId');
    }

    /**
     * @return array
     */
    public function provideCountAndViolation()
    {
        return array(
            array(1, 0),
            array(0, 1),
        );
    }
}
