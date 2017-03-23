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
     * @param mixed  $object
     * @param array  $optionalParameters
     *
     * @return boolean
     */
    public function isGranted($action, $object, array $optionalParameters = array())
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($action, $object)) {
                return $strategy->isGranted($action, $object, $optionalParameters);
            }
        }

        return true;
    }
}
