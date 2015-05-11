<?php

namespace OpenOrchestra\Backoffice\Manipulator;


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
        return 'open_orchestra_backoffice.display_block.strategy';
    }

    /**
     * @return string
     */
    protected function getServicePrefix()
    {
        return 'open_orchestra_backoffice.display';
    }
}
