<?php

namespace OpenOrchestra\Backoffice\Perimeter\Strategy;

use OpenOrchestra\Backoffice\Model\PerimeterInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;

/**
 * Class ContentTypePerimeterStrategy
 */
class ContentTypePerimeterStrategy implements PerimeterStrategyInterface
{
    /**
     * Return the supported perimeter type
     */
    public function getType()
    {
        return ContentTypeInterface::ENTITY_TYPE;
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
        return true;
    }
}
