<?php

namespace OpenOrchestra\Backoffice\BusinessRules\Strategies;

/**
 * class AbstractBusinessRulesStrategy
 */
abstract class AbstractBusinessRulesStrategy implements BusinessRulesStrategyInterface
{
    /**
     * @param string $action
     *
     * @return boolean
     */
    public function support($action)
    {
        return array_key_exists($action, $this->getActions());
    }

    /**
     * @param string $action
     * @param mixed  $entity
     *
     * @return boolean
     */
    public function isGranted($action, $entity, array $parameters)
    {
        $method = $this->getActions()[$action];

        return $this->$method($entity, $parameters);
    }

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return array
     */
    abstract public function getActions();
}
