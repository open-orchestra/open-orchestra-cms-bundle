<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\TrashcanRemoveValidator;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class TrashcanRemoveValidatorTest
 */
class TrashcanRemoveValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var TrashcanRemoveValidatorTest
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

        $this->validator = new TrashcanRemoveValidator();
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
     * @param int    $count
     *
     * @dataProvider provideDateAndTypeTrashItem
     */
    public function testValidate($dateTrashItem, $count)
    {
        Phake::when($this->trashItem)->getDeletedAt()->thenReturn($dateTrashItem);

        $this->validator->validate($this->trashItem, $this->constraint);

        Phake::verify($this->context, Phake::times($count))->buildViolation(Phake::anyParameters());
    }

    public function provideDateAndTypeTrashItem()
    {
        return array(
          "trash item content" => array(new \DateTime(), 1),
          "trash item node deleted now" => array(new \DateTime(), 1),
          "trash item node deleted yesterday" => array(new \DateTime('yesterday'), 1),
          "trash item node deleted 8 days" => array(new \DateTime('8 days ago'), 0),
        );
    }
}
