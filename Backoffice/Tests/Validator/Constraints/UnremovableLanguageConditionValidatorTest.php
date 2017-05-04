<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Backoffice\Validator\Constraints\UnremovableLanguageConditionValidator;

/**
 * Test UnremovableLanguageConditionValidatorTest
 */
class UnremovableLanguageConditionValidatorTest extends AbstractBaseTestCase
{
    protected $validator;

    protected $constraintViolationBuilder;
    protected $context;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilder');
        Phake::when($this->constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContext');
        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->validator = new UnremovableLanguageConditionValidator();
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
     * @param array $languages
     * @param int   $violationCount
     *
     * @dataProvider providePatternCount
     */
    public function testValidate(array $aliases, array $languages, $violationCount)
    {
        $constraint = Phake::mock('OpenOrchestra\Backoffice\Validator\Constraints\UnremovableLanguageCondition');
        Phake::when($constraint)->getLanguages()->thenReturn($languages);

        $this->validator->validate($aliases, $constraint);

        Phake::verify($this->constraintViolationBuilder, Phake::times($violationCount))->addViolation();
    }

    /**
     * @return array
     */
    public function providePatternCount()
    {
        $alias0 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        Phake::when($alias0)->getLanguage()->thenReturn('fr');
        $alias1 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        Phake::when($alias1)->getLanguage()->thenReturn('en');
        $alias2 = Phake::mock('OpenOrchestra\ModelInterface\Model\SiteAliasInterface');
        Phake::when($alias2)->getLanguage()->thenReturn('de');

        return array(
            array(array($alias0, $alias1, $alias2), array('fr', 'en', 'de'), 0),
            array(array($alias0, $alias1), array('fr', 'en', 'de'), 1),
            array(array($alias0, $alias1, $alias2), array('fr', 'en', 'de', 'es'), 1),
            array(array($alias0, $alias1, $alias2), array('fr', 'en'), 0),
        );
    }
}
