<?php

namespace OpenOrchestra\Backoffice\Reference;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\Reference\Strategies\ReferenceStrategyInterface;

/**
 * Class ReferenceManager
 */
class ReferenceManager
{
    protected $objectManager;
    protected $strategies = array();

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param ReferenceStrategyInterface $strategy
     */
    public function addStrategy(ReferenceStrategyInterface $strategy)
    {
        $this->strategies[] = $strategy;
    }

    /**
     * Update Keyword References
     *
     * @param mixed $entity
     */
    public function addReferencesToEntity($entity)
    {
        foreach ($this->strategies as $strategy) {
            $strategy->addReferencesToEntity($entity);
        }

         $this->objectManager->flush();
    }

    /**
     * @param mixed $entity
     */
    public function removeReferencesToEntity($entity)
    {
        foreach ($this->strategies as $strategy) {
            $strategy->removeReferencesToEntity($entity);
        }

         $this->objectManager->flush();
    }

    /**
     * @param mixed $entity
     */
    public function updateReferencesToEntity($entity)
    {
        $this->removeReferencesToEntity($entity);
        $this->addReferencesToEntity($entity);
    }
}