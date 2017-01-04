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
     * @return array
     */
    public function generatePerimeter();

    /**
     * get perimeter configuration
     *
     * @return array
     */
    public function getPerimeterConfiguration();
}
