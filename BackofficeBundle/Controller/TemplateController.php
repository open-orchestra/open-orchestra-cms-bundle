<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        if (empty($templateId)) {
            $templateClass = $this->container->getParameter('php_orchestra_model.document.template.class');
            $template = new $templateClass();
            $template->setSiteId('1');
            $template->setLanguage('fr');
        } else {
            $template = $templateRepository->findOneByTemplateId($templateId);
        }

        $form = $this->createForm(
            'template',
            $template,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_template_form', array('templateId' => $templateId))
            )
        );

        $form->handleRequest($request);

        $this->handleForm(
            $form,
            $this->get('translator')->trans('php_orchestra_backoffice.form.template.success'),
            $template
        );

        return $this->renderAdminForm($form);
    }
}
