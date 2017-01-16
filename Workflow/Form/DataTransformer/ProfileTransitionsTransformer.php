<?php

namespace OpenOrchestra\Workflow\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\Workflow\Factory\TransitionFactory;

/**
 * Class ProfileTransitionsTransformer
 */
class ProfileTransitionsTransformer implements DataTransformerInterface
{
    protected $statusRepository;
    protected $transitionFactory;
    protected $cachedStatuses = array();

    /**
     * @param StatusRepositoryInterface $statusRepository
     * @param string                    $transitionClass
     */
    public function __construct(StatusRepositoryInterface $statusRepository, TransitionFactory $transitionFactory)
    {
        $this->statusRepository = $statusRepository;
        $this->transitionFactory = $transitionFactory;
    }

    /**
     * @param array $value
     *
     * @return array
     */
    public function transform($value)
    {
        $transitions = array();

        foreach ($value as $transition) {
            $transitions[] = $transition->getStatusFrom()->getId() . '-' . $transition->getStatusTo()->getId();
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

        foreach ($value as $flatedTransition) {
            $statuses = explode('-', $flatedTransition);
            $statusFrom = $this->getStatus($statuses[0]);
            $statusTo = $this->getStatus($statuses[1]);
            if ($statusFrom instanceof StatusInterface && $statusTo instanceof StatusInterface) {
                $transitions[] = $this->transitionFactory->create($statusFrom, $statusTo);
            }
        }

        return $transitions;
    }

    /**
     * Retrieve a status from the cache or DB
     *
     * @param string $statusId
     *
     * @return mixed
     */
    protected function getStatus($statusId) {
        if (!isset($this->cachedStatuses[$statusId])) {
            $this->cachedStatuses[$statusId] = $this->statusRepository->findOneById($statusId);
        }

        return $this->cachedStatuses[$statusId];
    }
}
