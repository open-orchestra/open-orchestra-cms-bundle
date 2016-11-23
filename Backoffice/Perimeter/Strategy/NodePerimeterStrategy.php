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
     * @param string $item
     *
     * @return boolean
     */
    public function isInPerimeter($item, PerimeterInterface $perimeter)
    {
        if (is_string($item)) {
            foreach ($perimeter->getItems() as $path) {
                if (0 === strpos($path, $item)) {
                    return true;
                }
            }
        }

        return false;
    }
}