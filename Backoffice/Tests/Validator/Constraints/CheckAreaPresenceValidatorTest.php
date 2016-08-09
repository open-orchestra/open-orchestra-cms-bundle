<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Validator\Constraints\CheckAreaPresenceValidator;

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

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

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
     * Test not add violation
     */
    public function testAddNotViolation()
    {
        $area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($this->node)->getRootArea()->thenReturn($area);

        $this->validator->validate($this->node, $this->constraint);

        Phake::verify($this->constraintViolationBuilder, Phake::never())->atPath('templateId');
        Phake::verify($this->constraintViolationBuilder, Phake::never())->atPath('nodeSource');
    }

    /**
     * Test add violation
     */
    public function testAddViolation()
    {
        $this->validator->validate($this->node, $this->constraint);

        Phake::when($this->node)->getRootArea()->thenReturn(null);

        Phake::verify($this->constraintViolationBuilder)->atPath('nodeSource');
        Phake::verify($this->constraintViolationBuilder)->atPath('templateId');
    }
}
