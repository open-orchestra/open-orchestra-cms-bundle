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
        $this->strategies[$strategy->getType()] = $strategy;
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
        $entityType = defined(get_class($entity) . '::ENTITY_TYPE') ? $entity::ENTITY_TYPE : null;

        if (!is_null($entityType) && array_key_exists($entityType, $this->strategies) && $this->strategies[$entityType]->support($action)) {
            return  $this->strategies[$entityType]->isGranted($action, $entity, $parameters);
        }

        return true;
    }
}
