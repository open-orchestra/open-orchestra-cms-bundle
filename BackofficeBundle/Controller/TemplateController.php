<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

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
     * @Config\Route("/template/form/{templateId}", name="php_orchestra_backoffice_template_form", defaults={"templateId" = 0})
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $templateId)
    {
        $templateRepository = $this->container->get('php_orchestra_model.repository.template');

        $template = $templateRepository->findOneByTemplateId($templateId);

        $form = $this->generateTemplateForm(
            $template,
            $this->generateUrl('php_orchestra_backoffice_template_form', array('templateId' => $templateId))
        );

        $form->handleRequest($request);

        $this->handleForm(
            $form,
            $this->get('translator')->trans('php_orchestra_backoffice.form.template.success'),
            $template
        );

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/template/new", name="php_orchestra_backoffice_template_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $templateClass = $this->container->getParameter('php_orchestra_model.document.template.class');
        $context = $this->get('php_orchestra_backoffice.context_manager');

        $template = new $templateClass();
        $template->setSiteId($context->getCurrentSiteId());
        $template->setLanguage($context->getCurrentLocale());

        $form = $this->generateTemplateForm($template, $this->generateUrl('php_orchestra_backoffice_template_new'));

        $form->handleRequest($request);

        $this->handleForm(
            $form,
            $this->get('translator')->trans('php_orchestra_backoffice.form.template.success'),
            $template
        );

        if (!is_null($template->getTemplateId())) {
            $url = $this->generateUrl('php_orchestra_backoffice_template_form', array('templateId' => $template->getTemplateId()));

            return $this->redirect($url);
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param $template
     * @param $url
     *
     * @return Form
     */
    public function generateTemplateForm($template, $url)
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
