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
     * @return array
     */
    public function createTemplateSetChoices()
    {
        $choices = array();
        foreach ($this->templateSet as $key => $parameter) {
            $choices[$key] = $parameter['label'];
        }

        return $choices;
    }

    /**
     * @return array
     */
    public function createTemplateChoices()
    {
        $choices = array();
        foreach ($this->templateSet as $keyTemplateSet => $templateSetParameters) {
            foreach ($templateSetParameters['templates'] as $key => $template) {
                $choices[$keyTemplateSet][$key] = $template['label'];
            }
        }
        $choices['second group']['toto'] = 'toto';

        return $choices;
    }

    /**
     * @param string $templateId
     *
     * @return array
     * @throws NonExistingTemplateSetException
     */
    public function createTemplateChoicesWithTemplateSet($templateSetId)
    {
        if (!isset($this->templateSet[$templateSetId])) {
            throw new NonExistingTemplateException();
        }

        $choices = array();
        foreach ($this->templateSetparameters[$templateSetId]['templates'] as $key => $template) {
            $choices[$key] = $template['label'];
        }

        return $choices;
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
     * @param array $templateSet
     */
    public function setTemplateSet(array $templateSet)
    {
        $this->templateSet = $templateSet;
    }
}
