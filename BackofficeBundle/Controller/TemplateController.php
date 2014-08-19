<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class TemplateController
 */
class TemplateController extends Controller
{
    /**
     * @param Request $request
     * @param int     $templateId
     *
     * @Config\Route("/admin/template/form/{templateId}", name="php_orchestra_backoffice_template_form", defaults={"templateId" = 0})
     * @Config\Method({"GET", "POST"})
     *
     * @return JsonResponse|Response
     */
    public function formAction(Request $request, $templateId)
    {
        $templateRepository = $this->container->get('php_orchestra_model.repository.template');

        if (empty($templateId)) {
            $templateClass = $this->container->getParameter('php_orchestra_model.document.template.class');
            $template = new $templateClass();
            $template->setSiteId(1);
            $template->setLanguage('fr');
        } else {
            $template = $templateRepository->findOneByTemplateId($templateId);
            $template->setVersion($template->getVersion() + 1);
        }

        $form = $this->createForm(
            'template',
            $template,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_template_form', array('templateId' => $templateId))
            )
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->get('doctrine.odm.mongodb.document_manager');
            $em->persist($template);
            $em->flush();

            return $this->redirect($this->generateUrl('php_orchestra_cms_bo'));
        }

        return $this->render(
            'PHPOrchestraBackofficeBundle:Editorial:template.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }
}
