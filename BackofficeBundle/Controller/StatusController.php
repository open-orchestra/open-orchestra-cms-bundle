<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\ModelBundle\Document\Status;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPOrchestra\ModelBundle\Document\AbstractStatus;

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

        $url = $this->generateUrl('php_orchestra_backoffice_status_form', array('statusId' => $statusId));
        $message = $this->get('translator')->trans('php_orchestra_backoffice.form.status.success');
        return $this->formHandler($url, $request, $status, $message);
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

        $url = $this->generateUrl('php_orchestra_backoffice_status_new');
        $message = $this->get('translator')->trans('php_orchestra_backoffice.form.status.creation');
        return $this->formHandler($url, $request, $status, $message);
    }

    /**
     * @param String         $url
     * @param Request        $request
     * @param AbstractStatus $status
     * @param String         $message
     *
     * @return Response
     */
    protected function formHandler($url, Request $request, AbstractStatus $status, $message){
        $form = $this->createForm(
            'status',
            $status,
            array(
                'action' => $url,
            )
        );
        $form->handleRequest($request);
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($status);
            $documentManager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $message
            );
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
}
