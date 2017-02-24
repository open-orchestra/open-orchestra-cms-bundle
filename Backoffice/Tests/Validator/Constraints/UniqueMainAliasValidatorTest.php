<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Validator\Constraints\UniqueMainAlias;
use OpenOrchestra\Backoffice\Validator\Constraints\UniqueMainAliasValidator;

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

        $this->site = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteInterface');

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
     * @param array $siteAliases
     * @param int   $violationTimes
     *
     * @dataProvider provideCountAndViolation
     */
    public function testValidate(array $siteAliases, $violationTimes)
    {
        Phake::when($this->site)->getAliases()->thenReturn(new ArrayCollection($siteAliases));

        $this->validator->validate($this->site, $this->constraint);

        Phake::verify($this->constraintViolationBuilder, Phake::times($violationTimes))->addViolation();
    }

    /**
     * @return array
     */
    public function provideCountAndViolation()
    {
        $mainSiteAlias = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        Phake::when($mainSiteAlias)->isMain()->thenReturn(true);


        $siteAlias = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        Phake::when($siteAlias)->isMain()->thenReturn(false);

        return array(
            array(array(), 0),
            array(array($mainSiteAlias), 0),
            array(array($siteAlias), 0),
            array(array($mainSiteAlias, $mainSiteAlias), 2),
            array(array($mainSiteAlias, $siteAlias), 0),
            array(array($mainSiteAlias, $siteAlias, $mainSiteAlias), 2),
        );
    }
}
