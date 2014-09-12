<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\ModelBundle\Document\ContentType;
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
     * @Config\Route("/content-type/form/{contentTypeId}", name="php_orchestra_backoffice_content_type_form")
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

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('php_orchestra_backoffice.form.content_type.success')
            );
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/content-type/new", name="php_orchestra_backoffice_content_type_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $contentType = new ContentType();

        $form = $this->createForm(
            'content_type',
            $contentType,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_content_type_new', array())
            )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($contentType);
            $documentManager->flush();

            return $this->redirect(
                $this->generateUrl('homepage')
            );
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
