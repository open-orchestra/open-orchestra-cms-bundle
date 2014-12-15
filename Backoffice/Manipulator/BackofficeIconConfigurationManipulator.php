<?php

namespace PHPOrchestra\Backoffice\Manipulator;


/**
 * Class BackofficeIconConfigurationManipulator
 */
class BackofficeIconConfigurationManipulator extends ConfigurationManiplulator
{
    /**
     * @return string
     */
    protected function getFolder()
    {
        return 'DisplayIcon';
    }

    /**
     * @return string
     */
    protected function getTag()
    {
        return 'php_orchestra_backoffice.display_icon.strategy';
    }

    /**
     * @return string
     */
    protected function getServicePrefix()
    {
        return 'php_orchestra_backoffice.icon';
    }
}
