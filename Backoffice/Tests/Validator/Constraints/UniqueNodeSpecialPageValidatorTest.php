<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\UniqueNodeSpecialPage;
use OpenOrchestra\Backoffice\Validator\Constraints\UniqueNodeSpecialPageValidator;
use Phake;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;

/**
 * Class UniqueNodeSpecialPageValidatorTest
 */
class UniqueNodeSpecialPageValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var UniqueNodeSpecialPageValidator
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

        $this->constraint = new UniqueNodeSpecialPage();
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');
        Phake::when($this->constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        $this->validator = new UniqueNodeSpecialPageValidator($this->nodeRepository);
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
     * @param int $countNodes
     * @param int $violationTimes
     *
     * @dataProvider provideHasNodesAndCount
     */
    public function testAddViolationOrNot($countNodes, $violationTimes)
    {
        Phake::when($this->nodeRepository)->countOtherNodeWithSameSpecialPageName(Phake::anyParameters())->thenReturn($countNodes);
        Phake::when($this->node)->getSpecialPageName()->thenReturn('fakeName');
        $this->validator->validate($this->node, $this->constraint);

        Phake::verify($this->context, Phake::times($violationTimes))->buildViolation($this->constraint->message);
    }

    /**
     * @return array
     */
    public function provideHasNodesAndCount()
    {
        return array(
            array(1, 1),
            array(2, 1),
            array(0, 0),
        );
    }

    /**
     * Test valide with no page special name
     */
    public function testValidatorWithNoPageSpecial()
    {
        $this->validator->validate($this->node, $this->constraint);

        Phake::verify($this->context, Phake::never())->buildViolation($this->constraint->message);
    }

}
