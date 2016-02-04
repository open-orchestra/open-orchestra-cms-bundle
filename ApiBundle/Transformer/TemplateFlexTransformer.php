<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\ApiBundle\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\TemplateFlexInterface;

/**
 * Class TemplateFlexTransformer
 */
class TemplateFlexTransformer extends AbstractSecurityCheckerAwareTransformer
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
        foreach ($template->getAreas() as $area) {
            $facade->addArea($this->getTransformer('area')->transformFromFlexTemplate($area, $template));
        }

        $facade->id = $template->getId();
        $facade->name = $template->getName();
        $facade->siteId = $template->getSiteId();
        $facade->templateId = $template->getTemplateId();
        $facade->deleted = $template->isDeleted();
        $facade->editable = false;

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
