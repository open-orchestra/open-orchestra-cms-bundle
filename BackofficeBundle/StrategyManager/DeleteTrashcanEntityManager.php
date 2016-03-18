<?php

namespace OpenOrchestra\BackofficeBundle\StrategyManager;

use OpenOrchestra\Backoffice\DeleteTrashcanEntity\DeleteTrashCanEntityInterface;
use OpenOrchestra\ModelInterface\Model\SoftDeleteableInterface;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;

/**
 * Class DeleteTrashcanEntityManager
 */
class DeleteTrashcanEntityManager
{
    protected $strategies = array();

    /**
     * @param DeleteTrashCanEntityInterface $strategy
     */
    public function addStrategy(DeleteTrashCanEntityInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    /**
     * @param mixed $entity
     */
    public function delete($entity)
    {
        /** @var DeleteTrashCanEntityInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($entity)) {
                $strategy->delete($entity);
                break;
            }
        }
    }
}
