<?php

namespace PHPOrchestra\Backoffice\Manipulator;

use Doctrine\Common\Inflector\Inflector;
use PHPOrchestra\Backoffice\Exception\StrategyAlreadyCreatedException;
use Sensio\Bundle\GeneratorBundle\Manipulator\Manipulator;

/**
 * Class ConfigurationManiplulator
 */
abstract class ConfigurationManiplulator extends Manipulator
{
    protected $file;

    /**
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * @param string $blockName
     * @param string $blockNamespace
     *
     * @return bool
     * @throws StrategyAlreadyCreatedException
     */
    public function addResource($blockName, $blockNamespace)
    {
        $code = "services:\n";
        if (file_exists($this->file)) {
            $code = file_get_contents($this->file);
        }

        $className = Inflector::classify($blockName);
        $strategyName = Inflector::tableize($blockName);

        if (false !== strpos($code, $strategyName)) {
            throw new StrategyAlreadyCreatedException();
        }

        $code .= sprintf("    %s:\n",  $this->getServicePrefix() . '.' . $strategyName);
        $code .= sprintf("        class: %s\\%s\\Strategies\\%sStrategy\n", $blockNamespace, $this->getFolder(), $className);
        $code .= sprintf("        tags:\n");
        $code .= sprintf("            - { name: %s }\n", $this->getTag());

        if (false === file_put_contents($this->file, $code)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    abstract protected function getFolder();

    /**
     * @return string
     */
    abstract protected function getTag();

    /**
     * @return string
     */
    abstract protected function getServicePrefix();
}
