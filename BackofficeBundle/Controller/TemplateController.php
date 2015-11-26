<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplatePanelStrategy;
use OpenOrchestra\ModelInterface\Event\TemplateEvent;
use OpenOrchestra\ModelInterface\TemplateEvents;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TemplateController
 */
class TemplateController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param int     $templateId
     *
     * @Config\Route("/template/form/{templateId}", name="open_orchestra_backoffice_template_form", defaults={"templateId" = 0})
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_TREE_TEMPLATE')")
     *
     * @return Response
     */
    public function formAction(Request $request, $templateId)
    {
        $templateRepository = $this->container->get('open_orchestra_model.repository.template');

        $template = $templateRepository->findOneByTemplateId($templateId);

        $form = $this->createForm('oo_template', $template, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_template_form', array('templateId' => $templateId))
        ), TreeTemplatePanelStrategy::ROLE_ACCESS_UPDATE_TEMPLATE);

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.template.success');

        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(TemplateEvents::TEMPLATE_UPDATE, new TemplateEvent($template));
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/template/new", name="open_orchestra_backoffice_template_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_TREE_TEMPLATE')")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $templateClass = $this->container->getParameter('open_orchestra_model.document.template.class');
        $context = $this->get('open_orchestra_backoffice.context_manager');

        $template = new $templateClass();
        $template->setSiteId($context->getCurrentSiteId());
        $template->setLanguage($context->getCurrentLocale());

        $form = $this->createForm('oo_template', $template, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_template_new')
        ), TreeTemplatePanelStrategy::ROLE_ACCESS_CREATE_TEMPLATE);

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.template.success');

        if ($this->handleForm($form, $message, $template)) {
            $this->dispatchEvent(TemplateEvents::TEMPLATE_CREATE, new TemplateEvent($template));

            return $this->redirect($this->generateUrl('open_orchestra_backoffice_template_form', array(
                'templateId' => $template->getTemplateId()
            )));
        }

        return $this->renderAdminForm($form);
    }
}
