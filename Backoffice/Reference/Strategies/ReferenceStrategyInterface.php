<?php

namespace OpenOrchestra\Backoffice\Reference\Strategies;

/**
 * Interface ReferenceStrategy
 */
Interface ReferenceStrategyInterface
{
    /**
     * @param mixed $entity
     *
     * @return boolean
     */
    public function support($entity);

    /**
     * @param mixed $entity
     */
    public function addreferencesToEntity($entity);

    /**
     * @param mixed $entity
     */
    public function removeReferencesToEntity($entity);
}