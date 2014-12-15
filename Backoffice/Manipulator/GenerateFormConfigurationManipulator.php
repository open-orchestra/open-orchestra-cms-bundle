<?php

namespace PHPOrchestra\Backoffice\Manipulator;

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
        return 'php_orchestra_backoffice.generate_form.strategy';
    }

    /**
     * @return string
     */
    protected function getServicePrefix()
    {
        return 'php_orchestra_backoffice.generate_form';
    }
}
