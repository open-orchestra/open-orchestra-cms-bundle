<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\TrashcanDeleteNodeValidator;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class TrashcanDeleteNodeValidatorTest
 */
class TrashcanDeleteNodeValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var TrashcanDeleteNodeValidator
     */
    protected $validator;
    protected $context;
    protected $constraint;
    protected $constraintViolationBuilder;
    protected $trashItem;

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

        $this->trashItem = Phake::mock('OpenOrchestra\ModelInterface\Model\TrashItemInterface');

        $this->validator = new TrashcanDeleteNodeValidator();
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
     * @param string $dateTrashItem
     * @param string $type
     * @param int    $count
     *
     * @dataProvider provideDateAndTypeTrashItem
     */
    public function testValidate($dateTrashItem, $type, $count)
    {
        Phake::when($this->trashItem)->getDeletedAt()->thenReturn($dateTrashItem);
        Phake::when($this->trashItem)->getType()->thenReturn($type);

        $this->validator->validate($this->trashItem, $this->constraint);

        Phake::verify($this->context, Phake::times($count))->buildViolation(Phake::anyParameters());
    }

    public function provideDateAndTypeTrashItem()
    {
        return array(
          "trash item content" => array('now', 'content', 0),
          "trash item node deleted now" => array('now', 'node', 1),
          "trash item node deleted yesterday" => array('yesterday', 'node', 1),
          "trash item node deleted 8 days" => array('8 days ago', 'node', 0),
        );
    }
}
