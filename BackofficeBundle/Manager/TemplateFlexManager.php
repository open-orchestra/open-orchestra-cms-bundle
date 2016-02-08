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
    protected $areaFlexClass;

    /**
     * Constructor
     *
     * @param ContextManager $contextManager
     * @param string         $templateFlexClass
     * @param string         $areaFlexClass
     */
    public function __construct(ContextManager $contextManager, $templateFlexClass, $areaFlexClass)
    {
        $this->contextManager = $contextManager;
        $this->templateFlexClass = $templateFlexClass;
        $this->areaFlexClass = $areaFlexClass;
    }

    /**
     * @return TemplateFlexInterface
     */
    public function initializeNewTemplateFlex()
    {
        $defaultArea = new $this->areaFlexClass();
        $defaultArea->setAreaType(AreaFlexInterface::TYPE_ROOT);
        $defaultArea->setAreaId(AreaFlexInterface::ROOT_AREA_ID);
        $defaultArea->setLabel(AreaFlexInterface::ROOT_AREA_LABEL);

        $template = new $this->templateFlexClass();
        $template->setSiteId($this->contextManager->getCurrentSiteId());
        $template->setArea($defaultArea);

        return $template;
    }
}
