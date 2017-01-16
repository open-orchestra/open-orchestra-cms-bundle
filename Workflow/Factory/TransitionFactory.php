<?php

namespace OpenOrchestra\Workflow\Factory;

use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowTransitionInterface;
use OpenOrchestra\ModelBundle\Document\WorkflowTransition;

/**
 * Class TransitionFactory
 */
class TransitionFactory
{
    protected $transitionClass;

    /**
     * @param string $transitionClass
     */
    public function __construct($transitionClass)
    {
        if (!($transitionClass instanceof WorkflowTransitionInterface)) {
            $transitionClass = WorkflowTransition::class;
        }

        $this->transitionClass = $transitionClass;
    }

    /**
     * Create a transition
     *
     * @param StatusInterface $statusFrom
     * @param StatusInterface $statusTo
     *
     * @return WorkflowTransitionInterface
     */
    public function create(StatusInterface $statusFrom, StatusInterface $statusTo)
    {
        $transition = new $this->transitionClass();
        $transition->setStatusFrom($statusFrom);
        $transition->setStatusTo($statusTo);

        return $transition;
    }
}
