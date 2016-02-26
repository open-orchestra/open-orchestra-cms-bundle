<?php

namespace OpenOrchestra\Backoffice\Tests\Validator\Constraints;

use OpenOrchestra\Backoffice\Validator\Constraints\CheckVariableRoutePatternInMenu;
use OpenOrchestra\Backoffice\Validator\Constraints\CheckVariableRoutePatternInMenuValidator;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

/**
 * Class CheckVariableRoutePatternInMenuValidatorTest
 */
class CheckVariableRoutePatternInMenuValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var CheckVariableRoutePatternInMenuValidator
     */
    protected $validator;
    protected $node;
    protected $context;
    protected $constraint;
    protected $constraintViolationBuilder;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new CheckVariableRoutePatternInMenu();
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');
        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);
        Phake::when($this->constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);
        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');

        $this->validator = new CheckVariableRoutePatternInMenuValidator();
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
     * @param $inMenu
     * @param $inFooter
     * @param $routePattern
     * @param $violationTimes
     *
     * @dataProvider provideNodeAttributesAndCount
     */
    public function testAddViolationOrNot($inMenu, $inFooter, $routePattern, $violationTimes)
    {
        Phake::when($this->node)->isInMenu()->thenReturn($inMenu);
        Phake::when($this->node)->isInFooter()->thenReturn($inFooter);
        Phake::when($this->node)->getRoutePattern()->thenReturn($routePattern);

        $this->validator->validate($this->node, $this->constraint);

        Phake::verify($this->context, Phake::times($violationTimes))->buildViolation($this->constraint->message);
    }

    /**
     * @return array
     */
    public function provideNodeAttributesAndCount()
    {
        return array(
            array(true, true, 'pattern', 0),
            array(true, true, '{pattern}', 1),
            array(true, false, '{pattern}', 1),
            array(false, true, '{pattern}', 1),
            array(false, false, '{pattern}', 0),
        );
    }
}
