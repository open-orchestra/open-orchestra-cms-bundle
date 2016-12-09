<?php

namespace OpenOrchestra\Backoffice\Perimeter\Strategy;

use OpenOrchestra\Backoffice\Model\PerimeterInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class NodePerimeterStrategy
 */
class NodePerimeterStrategy implements PerimeterStrategyInterface
{
    /**
     * Return the supported perimeter type
     */
    public function getType()
    {
        return NodeInterface::ENTITY_TYPE;
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
            foreach ($perimeter->getItems() as $path) {
                if (0 === strpos($item, $path)) {
                    return true;
                }
            }
        }

        return false;
    }
}
