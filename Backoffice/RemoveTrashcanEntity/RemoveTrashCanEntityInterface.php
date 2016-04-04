<?php

namespace OpenOrchestra\Backoffice\RemoveTrashcanEntity;

/**
 * Interface RemoveTrashCanEntityInterface
 */
interface RemoveTrashCanEntityInterface
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
    public function remove($entity);

    /**
     * @return string
     */
    public function getName();
}
