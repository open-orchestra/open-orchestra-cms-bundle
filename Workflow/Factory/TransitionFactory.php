<?php

namespace OpenOrchestra\Workflow\Factory;

use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowTransitionInterface;
use OpenOrchestra\Backoffice\Exception\WrongClassException;

/**
 * Class TransitionFactory
 */
class TransitionFactory
{
    protected $transitionClass;

    /**
     * @param string $transitionClass
     * @throws WrongClassException
     */
    public function __construct($transitionClass)
    {
        if (!(is_a($transitionClass, 'OpenOrchestra\ModelInterface\Model\WorkflowTransitionInterface', true))) {
            throw new WrongClassException();
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
