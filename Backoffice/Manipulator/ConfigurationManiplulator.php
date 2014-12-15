<?php

namespace PHPOrchestra\Backoffice\Manipulator;

use Doctrine\Common\Inflector\Inflector;
use Sensio\Bundle\GeneratorBundle\Manipulator\Manipulator;

/**
 * Class ConfigurationManiplulator
 */
class ConfigurationManiplulator extends Manipulator
{
    protected $file;

    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    public function addResource($blockName)
    {
        $className = Inflector::classify($blockName);
        $strategyName = Inflector::tableize($blockName);
    }
}
