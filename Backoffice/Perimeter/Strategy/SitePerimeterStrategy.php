<?php

namespace OpenOrchestra\Backoffice\Perimeter\Strategy;

use OpenOrchestra\Backoffice\Model\PerimeterInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;

/**
 * Class SitePerimeterStrategy
 */
class SitePerimeterStrategy implements PerimeterStrategyInterface
{
    /**
     * Return the supported perimeter type
     */
    public function getType()
    {
        return SiteInterface::ENTITY_TYPE;
    }

    /**
     * Check if $item is contained in $perimeter
     *
     * @param string             $item
     * @param PerimeterInterface $perimeter
     *
     * @return boolean
     */
    public function isInPerimeter($item, PerimeterInterface $perimeter)
    {
        if ($perimeter->getType() == $this->getType() && is_string($item)) {
            return in_array($item, $perimeter->getItems());
        }

        return false;
    }
}
