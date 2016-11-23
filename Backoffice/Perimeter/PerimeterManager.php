<?php

namespace OpenOrchestra\Backoffice\Perimeter;

use OpenOrchestra\Backoffice\Model\PerimeterInterface;
use OpenOrchestra\Backoffice\Perimeter\Strategy\PerimeterStrategyInterface;

/**
 * Class PerimeterManager
 */
class PerimeterManager
{
    protected $strategies = array();

    /**
     * Add $strategy to the manager
     *
     * @param PerimeterStrategyInterface $strategy
     */
    public function addStrategy(PerimeterStrategyInterface $strategy)
    {
        $this->strategies[$strategy->getType()] = $strategy;
    }

    /**
     * Check if a path is contained in the perimeter
     *
     * @param string $path
     *
     * @return boolean
     */
    public function isInPerimeter($item, PerimeterInterface $perimeter)
    {
        if (isset($this->strategies[$perimeter->getType()])) {
            return $this->strategies[$perimeter->getType()]->isInPerimeter($item, $perimeter);
        }

        return false;
    }
}