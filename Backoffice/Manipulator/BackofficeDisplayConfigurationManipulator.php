<?php

namespace PHPOrchestra\Backoffice\Manipulator;


/**
 * Class BackofficeDisplayConfigurationManipulator
 */
class BackofficeDisplayConfigurationManipulator extends ConfigurationManiplulator
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
        return 'php_orchestra_backoffice.display';
    }
}
