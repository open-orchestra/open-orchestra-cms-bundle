<?php

namespace OpenOrchestra\Backoffice\RemoveTrashcanEntity;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;

/**
 * Interface RemoveTrashCanEntityInterface
 */
interface RemoveTrashCanEntityInterface
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
     * @return string
     */
    public function getName();
}
