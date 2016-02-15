<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\TemplateFlexInterface;

/**
 * Class TemplateFlexTransformer
 */
class TemplateFlexTransformer extends AbstractTransformer
{
    /**
     * @param TemplateFlexInterface $template
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($template)
    {
        if (!$template instanceof TemplateFlexInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();
        $facade->area = $this->getTransformer('area_flex')->transformFromTemplateFlex($template->getArea(), $template);
        $facade->id = $template->getId();
        $facade->name = $template->getName();
        $facade->siteId = $template->getSiteId();
        $facade->templateId = $template->getTemplateId();
        $facade->deleted = $template->isDeleted();
        $facade->editable = false;

        $facade->addLink('_self_form', $this->generateRoute('open_orchestra_backoffice_template_flex_form',
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
        return 'template_flex';
    }
}
