<?php

namespace OpenOrchestra\Backoffice\GeneratePerimeter\Strategy;

/**
 * Interface GeneratePerimeterStrategyInterface
 */
interface GeneratePerimeterStrategyInterface
{
    /**
     * Return the supported perimeter type
     *
     * @return string
     */
    public function getType();

    /**
     * Generate perimeter
     *
     * @param string $siteId
     * @return array
     */
    public function generatePerimeter($siteId);

    /**
     * get perimeter configuration
     *
     * @param string $siteId
     * @return array
     */
    public function getPerimeterConfiguration($siteId);
}
