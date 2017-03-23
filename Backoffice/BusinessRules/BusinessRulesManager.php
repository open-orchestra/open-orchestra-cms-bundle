<?php

namespace OpenOrchestra\Backoffice\BusinessRules;

use OpenOrchestra\Backoffice\BusinessRules\Strategies\BusinessRulesStrategyInterface;

/**
 * Class BusinessRulesManager
 */
class BusinessRulesManager
{
    protected $strategies = array();

    /**
     * Add $strategy to the manager
     *
     * @param BusinessRulesStrategyInterface $strategy
     */
    public function addStrategy(BusinessRulesStrategyInterface $strategy)
    {
        $this->strategies[] = $strategy;
    }

    /**
     * Check if object is granted
     *
     * @param string $action
     * @param mixed  $entity
     * @param array  $parameters
     *
     * @return boolean
     */
    public function isGranted($action, $entity, array $parameters = array())
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($action, $entity)) {
                return $strategy->isGranted($action, $entity, $parameters);
            }
        }

        return true;
    }
}
