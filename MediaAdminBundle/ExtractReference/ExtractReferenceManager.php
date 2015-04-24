<?php

namespace OpenOrchestra\MediaAdminBundle\ExtractReference;

use OpenOrchestra\MediaAdminBundle\Exceptions\ExtractReferenceStrategyNotFound;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;

/**
 * Class ExtractReferenceManager
 */
class ExtractReferenceManager
{
    protected $strategies = array();

    /**
     * @param ExtractReferenceInterface $strategy
     */
    public function addStrategy(ExtractReferenceInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    /**
     * @param StatusableInterface $statusableElement
     *
     * @return array
     *
     * @throws ExtractReferenceStrategyNotFound
     */
    public function extractReference(StatusableInterface $statusableElement)
    {
        /** @var ExtractReferenceInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($statusableElement)) {
                return $strategy->extractReference($statusableElement);
            }
        }

        throw new ExtractReferenceStrategyNotFound();
    }
}
