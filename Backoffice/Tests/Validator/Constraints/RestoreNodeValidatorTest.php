<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\RestoreNodeValidator;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Phake;

/**
 * Class RestoreNodeValidatorTest
 */
class RestoreNodeValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var RestoreNodeValidator
     */
    protected $validator;
    protected $context;
    protected $constraint;
    protected $constraintViolationBuilder;
    protected $nodeRepository;
    protected $nodeRoot;
    protected $nodeSon;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = Phake::mock('Symfony\Component\Validator\Constraint');
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');

        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);
        Phake::when($this->constraintViolationBuilder)->setParameters(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);
        Phake::when($this->constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->nodeRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface');

        $this->nodeRoot = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->nodeRoot)->getNodeId()->thenReturn(NodeInterface::ROOT_NODE_ID);
        Phake::when($this->nodeRoot)->getNodeType()->thenReturn(NodeInterface::TYPE_DEFAULT);

        $this->nodeSon = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->nodeSon)->getNodeId()->thenReturn('sonId');
        Phake::when($this->nodeSon)->getNodeType()->thenReturn(NodeInterface::TYPE_DEFAULT);
        Phake::when($this->nodeSon)->getParentId()->thenReturn(NodeInterface::ROOT_NODE_ID);
        Phake::when($this->nodeRepository)->findOneByNodeId(NodeInterface::ROOT_NODE_ID)->thenReturn($this->nodeRoot);

        $this->validator = new RestoreNodeValidator($this->nodeRepository);
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
     * Test validate with violation
     */
    public function testvalidateWithViolation()
    {
        Phake::when($this->nodeRoot)->isDeleted()->thenReturn(true);
        $this->validator->validate($this->nodeSon, $this->constraint);

        Phake::verify($this->context)->buildViolation(Phake::anyParameters());
    }

    /**
     * Test validate with no violation
     */
    public function testvalidateWithNoViolation()
    {
        Phake::when($this->nodeRoot)->isDeleted()->thenReturn(false);

        $this->validator->validate($this->nodeSon, $this->constraint);
        $this->validator->validate($this->nodeRoot, $this->constraint);
        Phake::verify($this->context, Phake::never())->buildViolation(Phake::anyParameters());
    }

}
