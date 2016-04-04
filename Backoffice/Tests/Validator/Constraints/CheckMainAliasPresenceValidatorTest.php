<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Validator\Constraints\CheckMainAliasPresenceValidator;

/**
 * Class CheckMainAliasPresenceValidatorTest
 */
class CheckMainAliasPresenceValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var CheckMainAliasPresenceValidator
     */
    protected $validator;

    protected $site;
    protected $siteAlias;
    protected $context;
    protected $constraint;
    protected $constraintViolationBuilder;

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

        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');

        $this->validator = new CheckMainAliasPresenceValidator();
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
     * @param array $aliases
     * @param int   $violationTimes
     *
     * @dataProvider provideCountAndViolation
     */
    public function testAddViolationOrNot($aliases, $violationTimes)
    {
        Phake::when($this->site)->getAliases()->thenReturn($aliases);
        $this->validator->validate($this->site, $this->constraint);

        Phake::verify($this->constraintViolationBuilder, Phake::times($violationTimes));
    }

    /**
     * @return array
     */
    public function provideCountAndViolation()
    {
        return array(
            array(array(Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface')), 0),
            array(array(), 1),
        );
    }
}
