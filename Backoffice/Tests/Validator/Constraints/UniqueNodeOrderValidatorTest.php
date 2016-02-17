<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use Phake;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\Backoffice\Validator\Constraints\UniqueNodeOrder;
use OpenOrchestra\Backoffice\Validator\Constraints\UniqueNodeOrderValidator;

/**
 * Class UniqueNodeOrderValidatorTest
 */
class UniqueNodeOrderValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var UniqueNodeOrderValidator
     */
    protected $validator;

    protected $node;
    protected $context;
    protected $constraint;
    protected $constraintViolationBuilder;
    protected $nodeRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');

        $this->constraint = new UniqueNodeOrder();
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');

        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        $this->validator = new UniqueNodeOrderValidator($this->nodeRepository);
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
     * @param bool $hasNodes
     * @param int  $violationTimes
     *
     * @dataProvider provideHasNodesAndCount
     */
    public function testAddViolationOrNot($hasNodes, $violationTimes)
    {
        Phake::when($this->nodeRepository)->hasOtherNodeWithSameParentAndOrder(Phake::anyParameters())->thenReturn($hasNodes);

        $this->validator->validate($this->node, $this->constraint);

        Phake::verify($this->context, Phake::times($violationTimes))->buildViolation($this->constraint->message);
    }

    /**
     * @return array
     */
    public function provideHasNodesAndCount()
    {
        return array(
            array(true, 1),
            array(false, 0),
        );
    }
}
