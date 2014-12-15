<?php

namespace PHPOrchestra\Backoffice\Manipulator;

/**
 * Class FrontDisplayConfigurationManipulator
 */
class FrontDisplayConfigurationManipulator extends ConfigurationManiplulator
{
    /**
     * @return string
     */
    protected function getFolder()
    {
        return 'DisplayBlock';
    }

    /**
     * @return string
     */
    protected function getTag()
    {
        return 'php_orchestra_display.display_block.strategy';
    }

    /**
     * @return string
     */
    protected function getServicePrefix()
    {
        return 'php_orchestra_display.display';
    }
}
