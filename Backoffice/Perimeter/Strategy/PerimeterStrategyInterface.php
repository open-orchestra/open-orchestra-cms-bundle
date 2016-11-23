<?php

namespace OpenOrchestra\Backoffice\Perimeter\Strategy;

use OpenOrchestra\Backoffice\Model\PerimeterInterface;

/**
 * Interface PerimeterStrategyInterface
 */
interface PerimeterStrategyInterface
{
    /**
     * Return the supported perimeter type
     */
    public function getType();

    /**
     * Check if $item is contained in $perimeter
     *
     * @param string $path
     *
     * @return boolean
     */
    public function isInPerimeter($item, PerimeterInterface $perimeter);
}