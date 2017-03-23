<?php

namespace OpenOrchestra\Backoffice\BusinessRules\Strategies;

/**
 * Interface BusinessRulesStrategyInterface
 */
Interface BusinessRulesStrategyInterface
{
    /**
     * @param string $action
     * @param mixed  $entity
     *
     * @return boolean
     */
    public function support($action, $entity);

    /**
     * @param string $action
     * @param mixed  $entity
     * @param array  $parameters
     *
     * @return boolean
     */
    public function isGranted($action, $entity, array $parameters);

    /**
     * @param mixed  $entity
     *
     * @return boolean
     */
    public function supportEntity($entity);

    /**
     * @return array
     */
    public function getActions();
}
