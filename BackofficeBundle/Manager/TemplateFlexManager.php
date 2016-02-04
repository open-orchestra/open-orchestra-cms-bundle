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
    protected $areaClass;

    /**
     * Constructor
     *
     * @param ContextManager $contextManager
     * @param string         $templateFlexClass
     * @param string         $areaClass
     */
    public function __construct(ContextManager $contextManager, $templateFlexClass, $areaClass)
    {
        $this->contextManager = $contextManager;
        $this->templateFlexClass = $templateFlexClass;
        $this->areaClass = $areaClass;
    }

    /**
     * @return TemplateFlexInterface
     */
    public function initializeNewTemplateFlex()
    {
        $defaultArea = new $this->areaClass();
        $defaultArea->setAreaId(self::DEFAULT_AREA_ID);
        $defaultArea->setLabel(self::DEFAULT_AREA_LABEL);

        $template = new $this->templateFlexClass();
        $template->setSiteId($this->contextManager->getCurrentSiteId());
        $template->addArea($defaultArea);

        return $template;
    }
}
