<?php

namespace OpenOrchestra\BackofficeBundle\StrategyManager;

use OpenOrchestra\Backoffice\RestoreEntity\RestoreEntityInterface;

/**
 * Class RestoreEntityManager
 */
class RestoreEntityManager
{
    protected $strategies = array();

    /**
     * @param RestoreEntityInterface $strategy
     */
    public function addStrategy(RestoreEntityInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    /**
     * @param mixed $entity
     */
    public function restore($entity)
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($entity)) {
                $strategy->restore($entity);
                break;
            }
        }
    }
}
