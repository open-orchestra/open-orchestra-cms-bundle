<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use Phake;
use OpenOrchestra\Backoffice\Validator\Constraints\ContentTypeFieldValidator;

/**
 * Class ContentTypeFieldValidatorTest
 */
class ContentTypeFieldValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var ContentTypeFieldValidator
     */
    protected $validator;

    protected $context;
    protected $constraint;
    protected $constraintViolationBuilder;
    protected $disallowedFieldName = 'name';

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

        $this->validator = new ContentTypeFieldValidator(array($this->disallowedFieldName));
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
     * @param ContentTypeInterface $contentType
     * @param int                  $violationTimes
     *
     * @dataProvider provideCountAndViolation
     */
    public function testAddViolationOrNot(ContentTypeInterface $contentType, $violationTimes)
    {
        $this->validator->validate($contentType, $this->constraint);

        Phake::verify($this->constraintViolationBuilder, Phake::times($violationTimes));
    }

    /**
     * @return array
     */
    public function provideCountAndViolation()
    {
        $fieldType = Phake::mock('OpenOrchestra\ModelInterface\Model\FieldTypeInterface');
        Phake::when($fieldType)->getFieldId()->thenReturn($this->disallowedFieldName);
        $contentType0 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($contentType0)->getFields()->thenReturn(array());
        $contentType1 = Phake::mock('OpenOrchestra\ModelInterface\Model\ContentTypeInterface');
        Phake::when($contentType1)->getFields()->thenReturn(array($fieldType));
        return array(
            array($contentType0, 0),
            array($contentType1, 1),
        );
    }
}
