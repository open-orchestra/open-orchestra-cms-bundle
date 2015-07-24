<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\ApiBundle\Facade\TemplateFacade;
use OpenOrchestra\ModelInterface\Model\TemplateInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;

/**
 * Class TemplateTransformer
 */
class TemplateTransformer extends AbstractTransformer
{
    /**
     * @param TemplateInterface $template
     *
     * @return TemplateFacade
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($template)
    {
        if (!$template instanceof TemplateInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = new TemplateFacade();
        foreach ($template->getAreas() as $area) {
            $facade->addArea($this->getTransformer('area')->transformFromTemplate($area, $template));
        }

        $facade->id = $template->getId();
        $facade->name = $template->getName();
        $facade->siteId = $template->getSiteId();
        $facade->templateId = $template->getTemplateId();
        $facade->language = $template->getLanguage();
        $facade->deleted = $template->getDeleted();
        $facade->boDirection = $template->getBoDirection();

        $facade->addLink('_self_form', $this->generateRoute('open_orchestra_backoffice_template_form',
            array('templateId' => $template->getTemplateId())
        ));

        $facade->addLink('_self_delete', $this->generateRoute('open_orchestra_api_template_delete',
            array('templateId' => $template->getTemplateId())
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
