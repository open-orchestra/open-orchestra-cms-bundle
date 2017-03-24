<?php

namespace OpenOrchestra\Backoffice\BusinessRules\Strategies;

/**
 * Interface BusinessRulesStrategyInterface
 */
Interface BusinessRulesStrategyInterface
{
    /**
     * @param string $action
     *
     * @return boolean
     */
    public function support($action);

    /**
     * @param string $action
     * @param mixed  $entity
     * @param array  $parameters
     *
     * @return boolean
     */
    public function isGranted($action, $entity, array $parameters);

    /**
     * @return string
     */
    public function getType();

    /**
     * @return array
     */
    public function getActions();
}
