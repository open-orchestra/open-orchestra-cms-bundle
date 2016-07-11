<?php

namespace OpenOrchestra\Backoffice\Manager;

use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\ModelInterface\Model\TemplateInterface;

/**
 * Class TemplateManager
 */
class TemplateManager
{
    protected $contextManager;
    protected $templateClass;
    protected $areaManager;

    /**
     * Constructor
     *
     * @param ContextManager  $contextManager
     * @param string          $templateClass
     * @param AreaManager $areaManager
     */
    public function __construct(ContextManager $contextManager, $templateClass, AreaManager $areaManager)
    {
        $this->contextManager = $contextManager;
        $this->templateClass = $templateClass;
        $this->areaManager = $areaManager;
    }

    /**
     * @return TemplateInterface
     */
    public function initializeNewTemplate()
    {
        $rootArea = $this->areaManager->initializeNewAreaRoot();

        $template = new $this->templateClass();
        $template->setSiteId($this->contextManager->getCurrentSiteId());
        $template->setArea($rootArea);

        return $template;
    }
}
