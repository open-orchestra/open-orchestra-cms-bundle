<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\Event\TemplateEvent;
use OpenOrchestra\ModelInterface\TemplateEvents;
use Symfony\Component\Form\Form;
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
     * @Config\Security("has_role('ROLE_PANEL_TREE_TEMPLATE')")
     *
     * @return Response
     */
    public function formAction(Request $request, $templateId)
    {
        $templateRepository = $this->container->get('open_orchestra_model.repository.template');

        $template = $templateRepository->findOneByTemplateId($templateId);

        $form = $this->generateTemplateForm(
            $template,
            $this->generateUrl('open_orchestra_backoffice_template_form', array('templateId' => $templateId))
        );

        $form->handleRequest($request);

        $this->handleForm(
            $form,
            $this->get('translator')->trans('open_orchestra_backoffice.form.template.success'),
            $template
        );

        $this->dispatchEvent(TemplateEvents::TEMPLATE_UPDATE, new TemplateEvent($template));

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/template/new", name="open_orchestra_backoffice_template_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_PANEL_TREE_TEMPLATE')")
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

        $form = $this->generateTemplateForm($template, $this->generateUrl('open_orchestra_backoffice_template_new'));

        $form->handleRequest($request);

        $this->handleForm(
            $form,
            $this->get('translator')->trans('open_orchestra_backoffice.form.template.success'),
            $template
        );

        $statusCode = 200;
        if ($form->getErrors()->count() > 0) {
            $statusCode = 400;
        } elseif (!is_null($template->getTemplateId())) {
            $url = $this->generateUrl('open_orchestra_backoffice_template_form', array('templateId' => $template->getTemplateId()));

            $this->dispatchEvent(TemplateEvents::TEMPLATE_CREATE, new TemplateEvent($template));

            return $this->redirect($url);
        }

        $response = new Response('', $statusCode, array('Content-type' => 'text/html; charset=utf-8'));

        return $this->render(
            'OpenOrchestraBackofficeBundle::form.html.twig',
            array('form' => $form->createView()),
            $response
        );
    }

    /**
     * @param $template
     * @param $url
     *
     * @return Form
     */
    protected function generateTemplateForm($template, $url)
    {
        $form = $this->createForm(
            'template',
            $template,
            array(
                'action' => $url
            )
        );

        return $form;
    }
}
