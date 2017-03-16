<?php

namespace OpenOrchestra\Backoffice\TrashcanEntity;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;

/**
 * Interface TrashCanEntityInterface
 */
interface TrashCanEntityInterface
{
    /**
     * @param TrashItemInterface $trashItem
     *
     * @return bool
     */
    public function support(TrashItemInterface $trashItem);

    /**
     * @param TrashItemInterface $trashItem
     */
    public function remove(TrashItemInterface $trashItem);

    /**
     * @param TrashItemInterface $trashItem
     */
    public function restore(TrashItemInterface $trashItem);

    /**
     * @return string
     */
    public function getName();
}
