<?php

namespace OpenOrchestra\BackofficeBundle\Manager;

use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;
use OpenOrchestra\ModelInterface\Model\TemplateFlexInterface;

/**
 * Class TemplateFlexManager
 */
class TemplateFlexManager
{
    protected $contextManager;
    protected $templateFlexClass;
    protected $areaManager;

    /**
     * Constructor
     *
     * @param ContextManager  $contextManager
     * @param string          $templateFlexClass
     * @param AreaFlexManager $areaManager
     */
    public function __construct(ContextManager $contextManager, $templateFlexClass, AreaFlexManager $areaManager)
    {
        $this->contextManager = $contextManager;
        $this->templateFlexClass = $templateFlexClass;
        $this->areaManager = $areaManager;
    }

    /**
     * @return TemplateFlexInterface
     */
    public function initializeNewTemplateFlex()
    {
        $rootArea = $this->areaManager->initializeNewAreaRoot();

        $template = new $this->templateFlexClass();
        $template->setSiteId($this->contextManager->getCurrentSiteId());
        $template->setArea($rootArea);

        return $template;
    }
}
