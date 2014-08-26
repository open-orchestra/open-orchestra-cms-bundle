<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class ContentTypeController
 */
class ContentTypeController extends Controller
{
    /**
     * @param Request $request
     * @param string  $contentTypeId
     *
     * @Config\Route("/admin/content-type/form/{contentTypeId}", name="php_orchestra_backoffice_content_type_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $contentTypeId)
    {
        $contentType = $this->get('php_orchestra_model.repository.content_type')->findOneBy(array('contentTypeId' => $contentTypeId));

        $form = $this->createForm(
            'content_type',
            $contentType,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_content_type_form', array(
                        'contentTypeId' => $contentTypeId,
                    ))
            )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($contentType);
            $documentManager->flush();

            return $this->redirect(
                $this->generateUrl('php_orchestra_cms_bo')
            );
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
