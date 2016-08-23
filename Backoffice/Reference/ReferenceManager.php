<?php

namespace OpenOrchestra\Backoffice\Reference;

use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\Reference\Strategie\ReferenceStrategyInterface;

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
     * @param StatusableInterface $statusableElement
     */
    public function addReferencesToEntity(StatusableInterface $statusableElement)
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($statusableElement)) {
                $strategy->addReferencesToEntity($statusableElement);
            }
        }

         $this->objectManager->flush();
    }

    /**
     * @param StatusableInterface $statusableElement
     */
    public function removeReferencesToEntity(StatusableInterface $statusableElement)
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($statusableElement)) {
                $strategy->removeReferencesToEntity($statusableElement);
            }
        }

         $this->objectManager->flush();
    }

    /**
     * @param StatusableInterface $statusableElement
     */
    public function updateReferencesToEntity(StatusableInterface $statusableElement)
    {
        $this->removeReferencesToEntity($statusableElement);
        $this->addReferencesToEntity($statusableElement);
    }
}