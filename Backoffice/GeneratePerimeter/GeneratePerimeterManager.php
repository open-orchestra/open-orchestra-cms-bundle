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
     * @return array
     */
    public function generatePerimeters()
    {
        $perimeters = array();
        foreach ($this->strategies as $strategy) {
            $perimeters = array_merge($perimeters, array($strategy->getType() => $strategy->generatePerimeter()));
        }

        return $perimeters;
    }

    /**
     * get perimeters configuration
     *
     * @return array
     */
    public function getPerimetersConfiguration()
    {
        $perimetersConfiguration = array();
        foreach ($this->strategies as $strategy) {
            $perimetersConfiguration = array_merge($perimetersConfiguration, array($strategy->getType() => $strategy->getPerimeterConfiguration()));
        }

        return $perimetersConfiguration;
    }

   static public function changePathToName($path) {
       return str_replace('/', self::DELIMITER, $path);
   }

   static public function changeNameToPath($name) {
       return str_replace(self::DELIMITER, '/', $name);
   }
}
