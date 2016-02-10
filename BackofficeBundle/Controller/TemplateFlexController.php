<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeTemplatePanelStrategy;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TemplateFlexController
 */
class TemplateFlexController extends AbstractAdminController
{

    /**
     * @param Request $request
     * @param int     $templateId
     *
     * @Config\Route("/template_flex/form/{templateId}", name="open_orchestra_backoffice_template_flex_form", defaults={"templateId" = 0})
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_TREE_TEMPLATE')")
     *
     * @return Response
     */
    public function formAction(Request $request, $templateId)
    {
        $templateRepository = $this->container->get('open_orchestra_model.repository.template_flex');

        $template = $templateRepository->findOneByTemplateId($templateId);

        $form = $this->createForm('oo_template_flex', $template, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_template_flex_form', array('templateId' => $templateId))
        ), TreeTemplatePanelStrategy::ROLE_ACCESS_UPDATE_TEMPLATE);

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.template.success');

        if ($this->handleForm($form, $message)) {
            //$this->dispatchEvent(TemplateEvents::TEMPLATE_UPDATE, new TemplateEvent($template));
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/template_flex/new", name="open_orchestra_backoffice_template_flex_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_TREE_TEMPLATE')")
     *
     * @return Response
     */
    public function newActionFlex(Request $request)
    {
        $templateManager = $this->container->get('open_orchestra_backoffice.manager.template_flex');
        $template = $templateManager->initializeNewTemplateFlex();

        $form = $this->createForm('oo_template_flex', $template, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_template_flex_new')
        ), TreeTemplatePanelStrategy::ROLE_ACCESS_CREATE_TEMPLATE);

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.template.success');

        if ($this->handleForm($form, $message, $template)) {
            //$this->dispatchEvent(TemplateEvents::TEMPLATE_CREATE, new TemplateEvent($template));

            return $this->redirect($this->generateUrl('open_orchestra_backoffice_template_flex_form', array(
                'templateId' => $template->getTemplateId()
            )));
        }

        return $this->renderAdminForm($form);
    }
}
