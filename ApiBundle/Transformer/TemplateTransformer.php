<?php

namespace PHPOrchestra\ApiBundle\Transformer;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ApiBundle\Facade\TemplateFacade;
use PHPOrchestra\ModelBundle\Model\TemplateInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

        $facade->name = $mixed->getName();
        $facade->siteId = $mixed->getSiteId();
        $facade->templateId = $mixed->getTemplateId();
        $facade->language = $mixed->getLanguage();
        $facade->status = $this->getTransformer('status')->transform($mixed->getStatus());
        $facade->deleted = $mixed->getDeleted();
        $facade->boDirection = $mixed->getBoDirection();

        $facade->addLink('_self_form', $this->getRouter()->generate('php_orchestra_backoffice_template_form',
            array('templateId' => $mixed->getTemplateId()),
            UrlGeneratorInterface::ABSOLUTE_URL
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
