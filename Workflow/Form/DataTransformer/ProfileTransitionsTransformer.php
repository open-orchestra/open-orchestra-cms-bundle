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
    const STATUS_SEPARATOR = '-';

    protected $statusRepository;
    protected $transitionFactory;
    protected $cachedStatuses = array();

    /**
     * @param StatusRepositoryInterface $statusRepository
     * @param TransitionFactory         $transitionFactory
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
            $transitions[] = $this->generateTransitionName($transition->getStatusFrom(), $transition->getStatusTo());
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

        foreach ($value as $flattened) {
            $statuses = $this->getTransitionStatusIds($flattened);
            $statusFrom = $this->getStatus($statuses['from']);
            $statusTo = $this->getStatus($statuses['to']);
            if ($statusFrom instanceof StatusInterface && $statusTo instanceof StatusInterface) {
                $transitions[] = $this->transitionFactory->create($statusFrom, $statusTo);
            }
        }

        return $transitions;
    }

    /**
     * Generate a transition name from $statusFrom and $statusTo
     *
     * @param StatusInterface $statusFrom
     * @param StatusInterface $statusTo
     *
     * @return string
     */
    public function generateTransitionName(StatusInterface $statusFrom, StatusInterface $statusTo)
    {
        return $statusFrom->getId() . self::STATUS_SEPARATOR . $statusTo->getId();
    }

    /**
     * Get the status ids from $transitionName
     *
     * @param string $transitionName
     *
     * @return mixed
     */
    public function getTransitionStatusIds($transitionName)
    {
        $temp = explode(self::STATUS_SEPARATOR, $transitionName);

        $statuses['from'] = $temp[0];
        $statuses['to'] = $temp[1];

        return $statuses;
    }

    /**
     * Retrieve a status from the cache or DB
     *
     * @param string $statusId
     *
     * @return mixed
     */
    protected function getStatus($statusId)
    {
        if (!isset($this->cachedStatuses[$statusId])) {
            $this->cachedStatuses[$statusId] = $this->statusRepository->findOneById($statusId);
        }

        return $this->cachedStatuses[$statusId];
    }
}
