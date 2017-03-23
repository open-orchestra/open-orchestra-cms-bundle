<?php

namespace OpenOrchestra\Backoffice\BusinessRules\Strategies;

/**
 * class AbstractBusinessRulesStrategy
 */
abstract class AbstractBusinessRulesStrategy implements BusinessRulesStrategyInterface
{
    /**
     * @param string $action
     * @param mixed  $object
     *
     * @return boolean
     */
    public function support($action, $object){
        return array_key_exists($action, $this->getActions()) && $this->supportObject($object);
    }

    /**
     * @param string $action
     * @param mixed  $object
     *
     * @return boolean
     */
    public function isGranted($action, $object, array $optionalParameters){
        $method = $this->getActions()[$action];

        return $this->$method($object, $optionalParameters);
    }

    /**
     * @param mixed $object
     *
     * @return boolean
     */
    abstract public function supportObject($object);

    /**
     * @return array
     */
    abstract public function getActions();
}
