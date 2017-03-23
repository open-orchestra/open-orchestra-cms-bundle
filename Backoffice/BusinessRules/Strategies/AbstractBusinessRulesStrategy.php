<?php

namespace OpenOrchestra\Backoffice\BusinessRules\Strategies;

/**
 * class AbstractBusinessRulesStrategy
 */
abstract class AbstractBusinessRulesStrategy implements BusinessRulesStrategyInterface
{
    /**
     * @param string $action
     * @param mixed  $entity
     *
     * @return boolean
     */
    public function support($action, $entity){
        return array_key_exists($action, $this->getActions()) && $this->supportEntity($entity);
    }

    /**
     * @param string $action
     * @param mixed  $entity
     *
     * @return boolean
     */
    public function isGranted($action, $entity, array $parameters){
        $method = $this->getActions()[$action];

        return $this->$method($entity, $parameters);
    }

    /**
     * @param mixed $entity
     *
     * @return boolean
     */
    abstract public function supportEntity($entity);

    /**
     * @return array
     */
    abstract public function getActions();
}
