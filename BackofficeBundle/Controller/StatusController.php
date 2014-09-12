<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\ModelBundle\Document\Status;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StatusController
 */
class StatusController extends Controller
{
    /**
     * @param Request $request
     * @param int     $statusId
     *
     * @Config\Route("/status/form/{statusId}", name="php_orchestra_backoffice_status_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $statusId)
    {
        $status = $this->get('php_orchestra_model.repository.status')->find($statusId);

        $form = $this->createForm(
            'status',
            $status,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_status_form', array(
                    'statusId' => $statusId,
                ))
            )
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($status);
            $documentManager->flush();

            return $this->redirect(
                $this->generateUrl('homepage')
            );
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/status/new", name="php_orchestra_backoffice_status_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $statusClass = $this->container->getParameter('php_orchestra_model.document.status.class');
        $status = new $statusClass();
        $form = $this->createForm(
            'status',
            $status,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_status_new'),
            )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($status);
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
