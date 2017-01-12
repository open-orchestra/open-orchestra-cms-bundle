<?php

namespace OpenOrchestra\Workflow\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use OpenOrchestra\ModelBundle\Document\WorkflowTransition;

/**
 * Class ProfileTransitionsTransformer
 */
class ProfileTransitionsTransformer implements DataTransformerInterface
{
    protected $statusRepository;

    /**
     * @param StatusRepositoryInterface $statusRepository
     */
    public function __construct(StatusRepositoryInterface $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }

    /**
     * Generate all possible combinations between statuses
     *
     * @return array
     */
    protected function generateTransitionGrid()
    {
        $transitions = array();

        foreach ($this->statusRepository->findNotOutOfWorkflow() as $statusFrom) {
            foreach ($this->statusRepository->findNotOutOfWorkflow() as $statusTo) {
                $transitions[$statusFrom->getId()][$statusTo->getId()] = array(
                    'exists' => false,
                    'statusFrom' => $statusFrom,
                    'statusTo' => $statusTo
                );
            }
        }

        return $transitions;
    }

    /**
     * @param array $value
     *
     * @return array
     */
    public function transform($value)
    {
        $transitions = $this->generateTransitionGrid();

        foreach ($value as $transition) {
            if (!isset($transitions[$transition->getStatusFrom()->getId()])) {
                $transitions[$transition->getStatusFrom()->getId()] = array();
            }

            $transitions[$transition->getStatusFrom()->getId()][$transition->getStatusTo()->getId()]['exists'] = true;
        }

        return $transitions;
    }

    /**
     * @param array $value
     *
     * @return array
     */
    public function reverseTransform($value)
    {
        $transitions = array();

        foreach ($value as $statusFromFamilly) {
            if (is_array($statusFromFamilly)) {
                foreach ($statusFromFamilly as $transitionSet) {
                    if ($transitionSet['exists']) {
                        $transition = new WorkflowTransition();
                        $transition->setStatusFrom($transitionSet['statusFrom']);
                        $transition->setStatusTo($transitionSet['statusTo']);
                        $transitions[] = $transition;
                    }
                }
            }
        }
// var_dump($transitions);
        return $transitions;
    }
}
