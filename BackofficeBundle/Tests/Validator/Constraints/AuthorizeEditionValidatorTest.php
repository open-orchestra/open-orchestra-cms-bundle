<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Validator\Constraints;

use OpenOrchestra\BackofficeBundle\Validator\Constraints\AuthorizeEditionValidator;
use Phake;

/**
 * Test AuthorizeEditionValidatorTest
 */
class AuthorizeEditionValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuthorizeEditionValidator
     */
    protected $validator;

    protected $constraintViolationBuilder;
    protected $authorizeEditionManager;
    protected $constraint;
    protected $context;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = Phake::mock('OpenOrchestra\BackofficeBundle\Validator\Constraints\AuthorizeEdition');

        $this->authorizeEditionManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\AuthorizeEditionManager');

        $this->constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);
        Phake::when($this->constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->validator = new AuthorizeEditionValidator($this->authorizeEditionManager);
        $this->validator->initialize($this->context);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $this->validator);
    }

    /**
     * @param string    $objectClass
     * @param bool      $editable
     * @param int       $violationTime
     * @param bool|null $statusChanged
     *
     * @dataProvider provideClassEditableStatusAndViolation
     */
    public function testValidate($objectClass, $editable, $violationTime, $statusChanged = null)
    {
        $object = Phake::mock($objectClass);
        if (!is_null($statusChanged)) {
            Phake::when($object)->hasStatusChanged()->thenReturn($statusChanged);
        }
        Phake::when($this->authorizeEditionManager)->isEditable($object)->thenReturn($editable);

        $this->validator->validate($object, $this->constraint);

        Phake::verify($this->context, Phake::times($violationTime))->buildViolation($this->constraint->message);
    }

    public function provideClassEditableStatusAndViolation()
    {
        return array(
            array('stdClass', true, 0),
            array('OpenOrchestra\ModelInterface\Model\StatusableInterface', false, 1),
            array('OpenOrchestra\ModelInterface\Model\StatusableInterface', true, 0),
            array('OpenOrchestra\ModelInterface\Model\StatusableInterface', false, 0, true),
            array('OpenOrchestra\ModelInterface\Model\StatusableInterface', true, 0, true),
            array('OpenOrchestra\ModelInterface\Model\StatusableInterface', false, 1, false),
            array('OpenOrchestra\ModelInterface\Model\StatusableInterface', true, 0, false),
        );
    }
}
