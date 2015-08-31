<?php

namespace OpenOrchestra\Backoffice\RestoreEntity;

/**
 * Interface RestoreEntityInterface
 */
interface RestoreEntityInterface
{
    /**
     * @param mixed $entity
     *
     * @return bool
     */
    public function support($entity);

    /**
     * @param mixed $entity
     */
    public function restore($entity);

    /**
     * @return string
     */
    public function getName();
}
