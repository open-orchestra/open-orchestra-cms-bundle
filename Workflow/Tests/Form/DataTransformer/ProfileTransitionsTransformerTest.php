<?php

namespace OpenOrchestra\Workflow\Tests\Form\Type;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use OpenOrchestra\Workflow\Form\DataTransformer\ProfileTransitionsTransformer;

/**
 * Class StatusTypeTest
 */
class ProfileTransitionsTransformerTest extends AbstractBaseTestCase
{
    protected $transformer;
    protected $statusRepository;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->statusRepository = Phake::mock('OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface');
        $transitionFactory = Phake::mock('OpenOrchestra\Workflow\Factory\TransitionFactory');
        $this->transformer = new ProfileTransitionsTransformer($this->statusRepository, $transitionFactory);
    }

    /**
     * Test transform
     *
     * @param array $transitions
     * @param array $expectedTransitions
     *
     * @dataProvider provideTransitions
     */
    public function testTransform(array $transitions, array $expectedTransitions)
    {
        $flattenTransitions = $this->transformer->transform($transitions);
        $this->assertSame($expectedTransitions, $flattenTransitions);
    }

    /**
     * Test transform
     *
     * @param array $expectedTransitions
     * @param array $flattenTransitions
     *
     * @dataProvider provideTransitions
     */
    public function testReverseTransform($expectedTransitions, $flattenTransitions)
    {
        foreach ($expectedTransitions as $transition) {
            Phake::when($this->statusRepository)->findOneById($transition->getStatusFrom()->getId())
                ->thenReturn($transition->getStatusFrom());
            Phake::when($this->statusRepository)->findOneById($transition->getStatusTo()->getId())
                ->thenReturn($transition->getStatusTo());
        }

        $transitions = $this->transformer->reverseTransform($flattenTransitions);
        $this->assertSameTransitions($expectedTransitions, $flattenTransitions);
    }

    /**
     * provide transitions
     *
     * @return array
     */
    public function provideTransitions()
    {
        $transition_1 = $this->generateTransition('from1', 'to2');
        $transition_2 = $this->generateTransition('from2', 'to3');
        $transition_3 = $this->generateTransition('from3', 'to1');

        return array(
            array(array(), array()),
            array(array($transition_1), array('from1-to2')),
            array(array($transition_1, $transition_2, $transition_3), array('from1-to2', 'from2-to3', 'from3-to1')),
        );
    }

    /**
     * Assert that $expectedTransitions is the correct Transitions representation of $flattenTransitions
     *
     * @param array $expectedTransitions
     * @param array $flattenTransitions
     */
    protected function assertSameTransitions(array $expectedTransitions, array $flattenTransitions)
    {
        foreach ($expectedTransitions as $transition) {
            $flattenTransition = $transition->getStatusFrom()->getId() . '-' . $transition->getStatusTo()->getId();
            $this->assertContains($flattenTransition, $flattenTransitions);
        }

        foreach ($flattenTransitions as $flattenTransition) {
            $idsStatus = explode('-', $flattenTransition);
            $transitionOk = false;

            foreach ($expectedTransitions as $transition) {
                if ($idsStatus[0] == $transition->getStatusFrom()->getId()
                    && $idsStatus[1] == $transition->getStatusTo()->getId()
                ) {
                    $transitionOk = true;
                    break;
                }
            }

            $this->assertTrue($transitionOk);
        }
    }

    /**
     * Generate a Phake transition
     *
     * @param string $fromId
     * @param string $toId
     *
     * @return Phake_IMock
     */
    protected function generateTransition($fromId, $toId)
    {
        $statusFrom = $this->generateStatus($fromId);
        $statusTo = $this->generateStatus($toId);

        $transition = Phake::mock('OpenOrchestra\ModelInterface\Model\WorkflowTransitionInterface');
        Phake::when($transition)->getStatusFrom()->thenReturn($statusFrom);
        Phake::when($transition)->getStatusTo()->thenReturn($statusTo);

        return $transition;
    }

    /**
     * Generate a Phake Status
     *
     * @param string $id
     *
     * @return Phake_IMock
     */
    protected function generateStatus($id)
    {
        $status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        Phake::when($status)->getId()->thenReturn($id);

        return $status;
    }
}
