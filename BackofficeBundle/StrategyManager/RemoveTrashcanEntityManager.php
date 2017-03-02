<?php

namespace OpenOrchestra\BackofficeBundle\StrategyManager;

use OpenOrchestra\Backoffice\RemoveTrashcanEntity\RemoveTrashCanEntityInterface;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;

/**
 * Class RemoveTrashcanEntityManager
 */
class RemoveTrashcanEntityManager
{
    protected $strategies = array();

    /**
     * @param RemoveTrashCanEntityInterface $strategy
     */
    public function addStrategy(RemoveTrashCanEntityInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    /**
     * @param TrashItemInterface $trashItem
     */
    public function remove(TrashItemInterface $trashItem)
    {
        /** @var RemoveTrashCanEntityInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($trashItem)) {
                $strategy->remove($trashItem);
                break;
            }
        }
    }
}
