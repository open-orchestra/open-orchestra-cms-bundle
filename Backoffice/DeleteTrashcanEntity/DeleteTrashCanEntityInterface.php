<?php

namespace OpenOrchestra\Backoffice\DeleteTrashcanEntity;


/**
 * Interface DeleteTrashCanEntityInterface
 */
interface DeleteTrashCanEntityInterface
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
    public function delete($entity);

    /**
     * @return string
     */
    public function getName();
}
