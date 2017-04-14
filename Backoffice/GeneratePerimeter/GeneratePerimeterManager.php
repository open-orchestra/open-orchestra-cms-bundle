<?php

namespace OpenOrchestra\Backoffice\GeneratePerimeter;

use OpenOrchestra\Backoffice\GeneratePerimeter\Strategy\GeneratePerimeterStrategyInterface;
/**
 * Class GeneratePerimeterManager
 */
class GeneratePerimeterManager
{
    const DELIMITER = '__';

    protected $strategies = array();

    /**
     * Add $strategy to the manager
     *
     * @param PerimeterStrategyInterface $strategy
     */
    public function addStrategy(GeneratePerimeterStrategyInterface $strategy)
    {
        $this->strategies[$strategy->getType()] = $strategy;
    }

    /**
     * Generate perimeters
     *
     * @param string $siteId
     * @return array
     */
    public function generatePerimeters($siteId)
    {
        $perimeters = array();
        foreach ($this->strategies as $strategy) {
            $perimeters = array_merge($perimeters, array($strategy->getType() => $strategy->generatePerimeter($siteId)));
        }

        return $perimeters;
    }

    /**
     * get perimeters configuration
     *
     * @param string $siteId
     * @return array
     */
    public function getPerimetersConfiguration($siteId)
    {
        $perimetersConfiguration = array();
        foreach ($this->strategies as $strategy) {
            $perimetersConfiguration = array_merge(
                $perimetersConfiguration,
                array($strategy->getType() => $strategy->getPerimeterConfiguration($siteId))
            );
        }

        return $perimetersConfiguration;
    }

    /**
     * @param string $path
     * @return string
     */
    static public function changePathToName($path) {
        return str_replace('/', self::DELIMITER, $path);
    }

    /**
     * @param string $name
     * @return string
     */
    static public function changeNameToPath($name) {
        return str_replace(self::DELIMITER, '/', $name);
    }
}
