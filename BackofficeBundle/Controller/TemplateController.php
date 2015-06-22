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
     * @Config\Security("has_role('ROLE_ACCESS_TREE_TEMPLATE')")
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
     * @Config\Security("has_role('ROLE_ACCESS_TREE_TEMPLATE')")
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
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.template.success');

        if ($this->handleForm($form, $message, $template)) {
            $url = $this->generateUrl('open_orchestra_backoffice_template_form', array('templateId' => $template->getTemplateId()));
            $this->dispatchEvent(TemplateEvents::TEMPLATE_CREATE, new TemplateEvent($template));

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
