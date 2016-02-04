<?php

namespace OpenOrchestra\BackofficeBundle\Manager;

use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\ModelInterface\Model\TemplateFlexInterface;

/**
 * Class TemplateFlexManager
 */
class TemplateFlexManager
{
    const DEFAULT_AREA_ID = 'node';
    const DEFAULT_AREA_LABEL = 'Node';

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
        $defaultArea->setAreaId(self::DEFAULT_AREA_ID);
        $defaultArea->setLabel(self::DEFAULT_AREA_LABEL);

        $template = new $this->templateFlexClass();
        $template->setSiteId($this->contextManager->getCurrentSiteId());
        $template->addArea($defaultArea);

        return $template;
    }
}
