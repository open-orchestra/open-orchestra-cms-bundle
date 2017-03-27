<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\UniqueBlockCodeValidator;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Phake;

/**
 * Test UniqueBlockCodeValidatorTest
 */
class UniqueBlockCodeValidatorTest extends AbstractBaseTestCase
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

        $this->repository = Phake::mock('OpenOrchestra\ModelInterface\Repository\BlockRepositoryInterface');
        $this->validator = new UniqueBlockCodeValidator($this->repository);
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
     * @param string               $code
     * @param BlockInterface       $block
     * @param BlockInterface       $blockCode
     * @param int                  $violationCount
     *
     * @dataProvider provideBlockCode
     */
    public function testValidate($code, $block, $blockCode, $violationCount)
    {
        $constraint = Phake::mock('OpenOrchestra\Backoffice\Validator\Constraints\UniqueBlockCode');
        $constraint->block = $block;
        Phake::when($this->repository)->findOneTransverseBlockByCodeAndLanguage(Phake::anyParameters())->thenReturn($blockCode);

        $this->validator->validate($code, $constraint);

        Phake::verify($this->constraintViolationBuilder, Phake::times($violationCount))->addViolation();
    }

    /**
     * @return array
     */
    public function provideBlockCode()
    {
        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block)->getId()->thenReturn('fakeId');
        Phake::when($block)->isTransverse()->thenReturn(true);
        Phake::when($block)->getLanguage()->thenReturn('fakeLanguage');

        $blockCode = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($blockCode)->getId()->thenReturn('blockCodeFakeId');
        Phake::when($blockCode)->getCode()->thenReturn('fakeCode');

        return array(
            array('', $block, $blockCode, 0),
            array(null, $block, $blockCode, 0),
            array('fakeCode', $block, $blockCode, 1),
            array('fakeCode', $block, $block, 0)
        );
    }
}
