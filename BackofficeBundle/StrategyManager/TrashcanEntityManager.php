<?php

namespace OpenOrchestra\BackofficeBundle\StrategyManager;

use OpenOrchestra\Backoffice\TrashcanEntity\TrashCanEntityInterface;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;

/**
 * Class TrashcanEntityManager
 */
class TrashcanEntityManager
{
    protected $strategies = array();

    /**
     * @param TrashCanEntityInterface $strategy
     */
    public function addStrategy(TrashCanEntityInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    /**
     * @param TrashItemInterface $trashItem
     */
    public function remove(TrashItemInterface $trashItem)
    {
        /** @var TrashCanEntityInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($trashItem)) {
                $strategy->remove($trashItem);
                break;
            }
        }
    }

    /**
     * @param TrashItemInterface $trashItem
     */
    public function restore(TrashItemInterface $trashItem)
    {
        /** @var TrashCanEntityInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($trashItem)) {
                $strategy->restore($trashItem);
                break;
            }
        }
    }
}
