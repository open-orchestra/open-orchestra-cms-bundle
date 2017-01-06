<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Validator\Constraints\UniqueMainAlias;
use OpenOrchestra\Backoffice\Validator\Constraints\UniqueMainAliasValidator;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * Test UniqueMainAliasValidatorTest
 */
class UniqueMainAliasValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var UniqueMainAliasValidator
     */
    protected $validator;

    protected $site;
    protected $context;
    protected $constraintViolationBuilder;
    protected $constraint;
    protected $siteAliases;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new UniqueMainAlias();

        $this->constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilder');
        Phake::when($this->constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContext');
        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->siteAliases = Phake::mock('Doctrine\Common\Collections\Collection');
        Phake::when($this->siteAliases)->filter(Phake::anyParameters())->thenReturn($this->siteAliases);

        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');
        Phake::when($this->site)->getAliases()->thenReturn($this->siteAliases);

        $this->validator = new UniqueMainAliasValidator();
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
     * @param int $count
     * @param int $violationTimes
     *
     * @dataProvider provideCountAndViolation
     */
    public function testValidate($count, $violationTimes)
    {
        Phake::when($this->siteAliases)->count()->thenReturn($count);

        $this->validator->validate($this->site, $this->constraint);

        Phake::verify($this->constraintViolationBuilder, Phake::times($violationTimes))->addViolation();
    }

    /**
     * @return array
     */
    public function provideCountAndViolation()
    {
        return array(
            array(0, 0),
            array(1, 0),
            array(2, 1),
            array(3, 1),
        );
    }
}
