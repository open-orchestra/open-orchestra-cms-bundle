<?php

namespace OpenOrchestra\ApiBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplatePanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;
use OpenOrchestra\ModelInterface\Model\TemplateInterface;

/**
 * Class TemplateTransformer
 */
class TemplateTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param TemplateInterface $template
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($template)
    {
        if (!$template instanceof TemplateInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();
        $facade->area = $this->getTransformer('area')->transformFromTemplate($template->getArea(), $template);
        $facade->id = $template->getId();
        $facade->name = $template->getName();
        $facade->siteId = $template->getSiteId();
        $facade->templateId = $template->getTemplateId();
        $facade->deleted = $template->isDeleted();
        $facade->editable = false;

        $facade->addLink('_self_form', $this->generateRoute('open_orchestra_backoffice_template_form',
            array('templateId' => $template->getTemplateId())
        ));

        if ($this->authorizationChecker->isGranted(TreeTemplatePanelStrategy::ROLE_ACCESS_DELETE_TEMPLATE, $template)) {
            $facade->addLink('_self_delete', $this->generateRoute('open_orchestra_api_template_delete',
                array('templateId' => $template->getTemplateId())
            ));
        }

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
