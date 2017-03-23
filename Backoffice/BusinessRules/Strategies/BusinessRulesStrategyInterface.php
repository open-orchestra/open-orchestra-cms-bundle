<?php

namespace OpenOrchestra\Backoffice\BusinessRules\Strategies;

/**
 * Interface BusinessRulesStrategyInterface
 */
Interface BusinessRulesStrategyInterface
{
    /**
     * @param string $action
     * @param mixed  $object
     *
     * @return boolean
     */
    public function support($action, $object);

    /**
     * @param string $action
     * @param mixed  $object
     * @param array  $optionalParameters
     *
     * @return boolean
     */
    public function isGranted($action, $object, array $optionalParameters);

    /**
     * @param mixed  $object
     *
     * @return boolean
     */
    public function supportObject($object);

    /**
     * @return array
     */
    public function getActions();
}
