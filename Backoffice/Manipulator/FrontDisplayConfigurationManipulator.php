<?php

namespace OpenOrchestra\Backoffice\Manipulator;

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
        return 'open_orchestra_display.display_block.strategy';
    }

    /**
     * @return string
     */
    protected function getServicePrefix()
    {
        return 'open_orchestra_display.display';
    }
}
