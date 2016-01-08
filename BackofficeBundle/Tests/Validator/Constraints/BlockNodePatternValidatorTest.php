<?php

namespace OpenOrchestra\BackofficeBundle\Tests\Validator\Constraints;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;

use OpenOrchestra\BackofficeBundle\Validator\Constraints\BlockNodePatternValidator;

/**
 * Class BlockNodePatternValidatorTest
 */
class BlockNodePatternValidatorTest extends AbstractBaseTestCase
{
    /**
     * @var BlockNodePatternValidator
     */
    protected $validator;

    protected $context;
    protected $constraint;
    protected $generateFormManager;
    protected $constraintViolationBuilder;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = Phake::mock('OpenOrchestra\BackofficeBundle\Validator\Constraints\BlockNodePattern');
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        $this->constraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');
        $this->generateFormManager = Phake::mock('OpenOrchestra\BackofficeBundle\StrategyManager\GenerateFormManager');

        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);
        Phake::when($this->constraintViolationBuilder)->setParameters(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);
        Phake::when($this->constraintViolationBuilder)->atPath(Phake::anyParameters())->thenReturn($this->constraintViolationBuilder);

        $this->validator = new BlockNodePatternValidator($this->generateFormManager);
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
     * @param int    $violationTimes
     * @param array  $parameters
     * @param string $blockLabel
     * @param string $routePattern
     *
     * @dataProvider provideViolationsParametersLabelAndPattern
     */
    public function testValidate($violationTimes, $parametersTimes, $parametersViolations, $parameters, $blockLabel, $routePattern)
    {
        $block = Phake::mock('OpenOrchestra\ModelInterface\Model\BlockInterface');
        Phake::when($block)->getLabel()->thenReturn($blockLabel);
        $blocks = array($block, $block);

        $area = Phake::mock('OpenOrchestra\ModelInterface\Model\AreaInterface');
        Phake::when($area)->getBlocks()->thenReturn(array(
            array('nodeId' => 0, 'blockId' => 0),
            array('nodeId' => 0, 'blockId' => 1),
        ));
        $areas = array($area);

        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status)->isPublished()->thenReturn(true);

        $node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($node)->getStatus()->thenReturn($status);
        Phake::when($node)->getBlocks()->thenReturn($blocks);
        Phake::when($node)->getAreas()->thenReturn($areas);
        Phake::when($node)->getRoutePattern()->thenReturn($routePattern);

        Phake::when($this->generateFormManager)->getRequiredUriParameter(Phake::anyParameters())->thenReturn($parameters);

        $this->validator->validate($node, $this->constraint);

        Phake::verify($this->context, Phake::times($violationTimes))->buildViolation($this->constraint->message);
        foreach ($parametersViolations as $parameter) {
            Phake::verify($this->constraintViolationBuilder, Phake::times($parametersTimes))->setParameters(array(
                '%blockLabel%' => $blockLabel,
                '%parameter%' => $parameter,
            ));
        }
        Phake::verify($this->constraintViolationBuilder, Phake::times($violationTimes))->atPath('routePattern');
        Phake::verify($this->constraintViolationBuilder, Phake::times($violationTimes))->addViolation();
    }

    /**
     * @return array
     */
    public function provideViolationsParametersLabelAndPattern()
    {
        return array(
            array(0, 0, array(), array(), 'fooLabel', ''),
            array(2, 2, array('foo'), array('foo'), 'barLabel', ''),
            array(4, 2, array('foo', 'bar'), array('foo', 'bar'), 'fooLabel', ''),
            array(4, 2, array('foo', 'bar'), array('foo', 'bar'), 'fooLabel', 'foo'),
            array(2, 2, array('bar'), array('foo', 'bar'), 'fooLabel', '{foo}'),
            array(0, 0, array(), array('foo', 'bar'), 'fooLabel', '{foo}/{bar}'),
            array(0, 0, array(), array('foo', 'bar'), 'fooLabel', '{bar}/{foo}'),
            array(0, 0, array(), array('foo', 'bar'), 'fooLabel', '{bar}{foo}'),
        );
    }
}
