<?php

namespace OpenOrchestra\BackofficeBundle\StrategyManager;

use OpenOrchestra\Backoffice\RemoveTrashcanEntity\RemoveTrashCanEntityInterface;

/**
 * Class RemoveTrashcanEntityManager
 */
class RemoveTrashcanEntityManager
{
    protected $strategies = array();

    /**
     * @param RemoveTrashCanEntityInterface $strategy
     */
    public function addStrategy(RemoveTrashCanEntityInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    /**
     * @param mixed $entity
     */
    public function remove($entity)
    {
        /** @var RemoveTrashCanEntityInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($entity)) {
                $strategy->remove($entity);
                break;
            }
        }
    }
}
