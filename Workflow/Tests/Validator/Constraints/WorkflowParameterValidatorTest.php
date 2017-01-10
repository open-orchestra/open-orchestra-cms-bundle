<?php

namespace OpenOrchestra\Workflow\Tests\Validator\Constraints;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraint;
use OpenOrchestra\Workflow\Validator\Constraints\WorkflowParameterValidator;

/**
 * Class WorkflowParameterTest
 */
class WorkflowParameterValidatorTest extends AbstractBaseTestCase
{
    protected $validator;
    protected $translator;
    protected $constraint;
    protected $constraintRequiredMessage = 'required';
    protected $constraintUniqueMessage = 'unique';
    protected $context;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->translator = Phake::Mock('Symfony\Component\Translation\TranslatorInterface');
        $this->constraint = Phake::Mock('OpenOrchestra\Workflow\Validator\Constraints\WorkflowParameter');
        $this->constraint->requiredParameterMessage = $this->constraintRequiredMessage;
        $this->constraint->uniqueParameterMessage = $this->constraintUniqueMessage;

        $contraintViolationBuilder = Phake::mock('Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface');
        Phake::when($contraintViolationBuilder)->setParameter(Phake::anyParameters())->thenReturn($contraintViolationBuilder);
        $this->context = Phake::Mock('Symfony\Component\Validator\Context\ExecutionContextInterface');
        Phake::when($this->context)->buildViolation(Phake::anyParameters())->thenReturn($contraintViolationBuilder);

        $this->validator = new WorkflowParameterValidator($this->translator);
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
     * Test validated
     *
     * @param array   $statusCollection
     * @param integer $expectedRequire
     * @param integer $expectedUnique
     *
     * @dataProvider provideStatuses
     */
    public function testValidate(array $statusCollection, $expectedRequire, $expectedUnique)
    {
        $this->validator->validate($statusCollection, $this->constraint);

        Phake::verify($this->context, Phake::times($expectedRequire))->buildViolation($this->constraintRequiredMessage);
        Phake::verify($this->context, Phake::times($expectedUnique))->buildViolation($this->constraintUniqueMessage);
    }

    /**
     * @return array
     */
    public function provideStatuses()
    {
        $fullStatus = $this->generateStatus(array(
            'initialState'       => true,
            'translationState'   => true,
            'publishedState'     => true,
            'autoPublishState'   => true,
            'autoUnpublishState' => true
        ));

        $fullStatus2 = $this->generateStatus(array(
            'initialState'       => true,
            'translationState'   => true,
            'publishedState'     => true,
            'autoPublishState'   => true,
            'autoUnpublishState' => true
        ));

        $emptyStatus = $this->generateStatus(array(
            'initialState'       => false,
            'translationState'   => false,
            'publishedState'     => false,
            'autoPublishState'   => false,
            'autoUnpublishState' => false
        ));

        $status1 = $this->generateStatus(array(
            'initialState'       => true,
            'translationState'   => false,
            'publishedState'     => false,
            'autoPublishState'   => false,
            'autoUnpublishState' => true
        ));

        $status2 = $this->generateStatus(array(
            'initialState'       => false,
            'translationState'   => true,
            'publishedState'     => false,
            'autoPublishState'   => true,
            'autoUnpublishState' => false
        ));

        $status3 = $this->generateStatus(array(
            'initialState'       => false,
            'translationState'   => false,
            'publishedState'     => true,
            'autoPublishState'   => false,
            'autoUnpublishState' => false
        ));

        $status4 = $this->generateStatus(array(
            'initialState'       => true,
            'translationState'   => false,
            'publishedState'     => false,
            'autoPublishState'   => true,
            'autoUnpublishState' => false
        ));

        return array(
            '1 status ok'       => array(array($fullStatus)                 , 0, 0),
            '1 status required' => array(array($emptyStatus)                , 5, 0),
            '2 status unique'   => array(array($fullStatus, $fullStatus2)   , 0, 5),
            '3 statuses ok'     => array(array($status1, $status2, $status3), 0, 0),
            '3 statuses ko'     => array(array($status1, $status4, $status3), 1, 1),
        );
    }

    /**
     * Generate a phake status
     *
     * @param array $parameters
     *
     * @return Phake_IMock
     */
    protected function generateStatus(array $parameters)
    {
        $status = Phake::mock('OpenOrchestra\ModelBundle\Document\Status');

        Phake::when($status)->isInitialState()->thenReturn($parameters['initialState']);
        Phake::when($status)->isTranslationState()->thenReturn($parameters['translationState']);
        Phake::when($status)->isPublishedState()->thenReturn($parameters['publishedState']);
        Phake::when($status)->isAutoPublishFromState()->thenReturn($parameters['autoPublishState']);
        Phake::when($status)->isAutoUnpublishToState()->thenReturn($parameters['autoUnpublishState']);

        return $status;
    }
}
