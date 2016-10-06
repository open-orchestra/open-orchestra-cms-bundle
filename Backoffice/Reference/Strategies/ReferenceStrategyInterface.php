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
    public function addReferencesToEntity($entity, $subEntity);

    /**
     * @param mixed $entity
     * @param mixed $subEntity
     */
    public function removeReferencesToEntity($entity);
}
