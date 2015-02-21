<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Facade\TemplateFacade;
use OpenOrchestra\ModelInterface\Model\TemplateInterface;

/**
 * Class TemplateTransformer
 */
class TemplateTransformer extends AbstractTransformer
{
    /**
     * @param TemplateInterface $mixed
     *
     * @return TemplateFacade
     */
    public function transform($mixed)
    {
        $facade = new TemplateFacade();

        foreach ($mixed->getAreas() as $area) {
            $facade->addArea($this->getTransformer('area')->transformFromTemplate($area, $mixed));
        }

        $facade->id = $mixed->getId();
        $facade->name = $mixed->getName();
        $facade->siteId = $mixed->getSiteId();
        $facade->templateId = $mixed->getTemplateId();
        $facade->language = $mixed->getLanguage();
        $facade->deleted = $mixed->getDeleted();
        $facade->boDirection = $mixed->getBoDirection();

        $facade->addLink('_self_form', $this->generateRoute('open_orchestra_backoffice_template_form',
            array('templateId' => $mixed->getTemplateId())
        ));

        $facade->addLink('_self_delete', $this->generateRoute('open_orchestra_api_template_delete',
            array('templateId' => $mixed->getTemplateId())
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'template';
    }

}
