<?php

namespace OpenOrchestra\Backoffice\Manager;

use OpenOrchestra\Backoffice\Exception\NonExistingTemplateException;


/**
 * Class TemplateManager
 */
class TemplateManager
{
    protected $templateSet = array();

    /**
     * @param string $name
     * @param string $templateSetName
     *
     * @return string
     * @throws NonExistingTemplateException
     */
    public function getTemplateLabel($name, $templateSetName)
    {
        if (
            isset($this->templateSet[$templateSetName]) &&
            isset($this->templateSet[$templateSetName]['templates']) &&
            isset($this->templateSet[$templateSetName]['templates'][$name])
        ) {
            return $this->templateSet[$templateSetName]['templates'][$name]['label'];
        }

        throw new NonExistingTemplateException();
    }

    /**
     * @return array
     */
    public function getTemplateSetParameters()
    {
        return $this->templateSet;
    }

    /**
     * @param string $name
     * @param string $templateSetName
     *
     * @return string
     * @throws NonExistingTemplateException
     */
    public function getTemplatePath($name, $templateSetName)
    {
        if (
            isset($this->templateSet[$templateSetName]) &&
            isset($this->templateSet[$templateSetName]['templates']) &&
            isset($this->templateSet[$templateSetName]['templates'][$name])
        ) {
            return $this->templateSet[$templateSetName]['templates'][$name]['path'];
        }

        throw new NonExistingTemplateException();
    }

    /**
     * @param string $name
     * @param string $templateSetName
     *
     * @return array
     *
     * @throws NonExistingTemplateException
     */
    public function getTemplateAreas($name, $templateSetName)
    {
        if (
            isset($this->templateSet[$templateSetName]) &&
            isset($this->templateSet[$templateSetName]['templates']) &&
            isset($this->templateSet[$templateSetName]['templates'][$name])
        ) {
            return $this->templateSet[$templateSetName]['templates'][$name]['areas'];
        }

        throw new NonExistingTemplateException();
    }

    /**
     * @param array $templateSet
     */
    public function setTemplateSet(array $templateSet)
    {
        $this->templateSet = $templateSet;
    }
}
