<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Symfony\Component\Validator\Context\ExecutionContext;
use OpenOrchestra\Backoffice\Validator\Constraints\UniqueRedirectionValidator;

/**
 * Test UniqueRedirectionValidatorTest
 */
class UniqueRedirectionValidatorTest extends AbstractBaseTestCase
{
    protected $validator;

    protected $constraintViolationBuilder;
    protected $context;
    protected $repository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilder');
        Phake::when($this->constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContext');
        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->repository = Phake::mock('OpenOrchestra\ModelInterface\Repository\RedirectionRepositoryInterface');
        $this->validator = new UniqueRedirectionValidator($this->repository);
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
     * @param RedirectionInterface $redirection
     * @param int                  $violationCount
     *
     * @dataProvider providePatternCount
     */
    public function testValidate($count, $violationCount)
    {
        $redirection = Phake::mock('OpenOrchestra\ModelInterface\Model\RedirectionInterface');
        $constraint = Phake::mock('OpenOrchestra\Backoffice\Validator\Constraints\UniqueRedirection');
        Phake::when($this->repository)->countByPattern(Phake::anyParameters())->thenReturn($count);

        $this->validator->validate($redirection, $constraint);

        Phake::verify($this->constraintViolationBuilder, Phake::times($violationCount))->addViolation();
    }

    /**
     * @return array
     */
    public function providePatternCount()
    {
        return array(
            array(0, false),
            array(1, true),
            array(5, true)
        );
    }
}
