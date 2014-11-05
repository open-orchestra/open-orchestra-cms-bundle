<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPOrchestra\ModelBundle\Document\Content;

/**
 * Class ContentController
 */
class ContentController extends Controller
{
    /**
     * @param Request $request
     * @param int     $contentId
     *
     * @Config\Route("/content/form/{contentId}", name="php_orchestra_backoffice_content_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $contentId)
    {
        $content = $this->get('php_orchestra_model.repository.content')->findOneByContentId($contentId);

        $form = $this->createForm(
            'content',
            $content,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_content_form', array(
                    'contentId' => $contentId,
                ))
            )
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($content);
            $documentManager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('php_orchestra_backoffice.form.content.success')
            );
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/content/new/{contentType}", name="php_orchestra_backoffice_content_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request, $contentType)
    {
        $content = new Content();
        $content->setContentType($contentType);
        $form = $this->createForm(
            'content',
            $content,
            array(
                'action' => $this->generateUrl(
                    'php_orchestra_backoffice_content_new',
                    array('contentType' => $contentType)
                ),
                'method' => 'POST',
            )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($content);
            $documentManager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('php_orchestra_backoffice.form.content.creation')
            );

            return $this->redirect(
                $this->generateUrl('php_orchestra_backoffice_content_form', array(
                    'contentId' => $content->getId(),
                ))
            );
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
