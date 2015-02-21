<?php

namespace OpenOrchestra\Backoffice\Manipulator;


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
        return 'open_orchestra_backoffice.display_icon.strategy';
    }

    /**
     * @return string
     */
    protected function getServicePrefix()
    {
        return 'open_orchestra_backoffice.icon';
    }
}
