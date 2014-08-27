<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

            return $this->redirect(
                $this->generateUrl('homepage')
            );
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
