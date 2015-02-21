<?php

namespace OpenOrchestra\Backoffice\Manipulator;

/**
 * Class GenerateFormConfigurationManipulator
 */
class GenerateFormConfigurationManipulator extends ConfigurationManiplulator
{
    /**
     * @return string
     */
    protected function getFolder()
    {
        return 'GenerateForm';
    }

    /**
     * @return string
     */
    protected function getTag()
    {
        return 'open_orchestra_backoffice.generate_form.strategy';
    }

    /**
     * @return string
     */
    protected function getServicePrefix()
    {
        return 'open_orchestra_backoffice.generate_form';
    }
}
