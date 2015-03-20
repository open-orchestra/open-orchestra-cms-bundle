<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\Event\StatusEvent;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\StatusEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StatusController
 */
class StatusController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param int     $statusId
     *
     * @Config\Route("/status/form/{statusId}", name="open_orchestra_backoffice_status_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $statusId)
    {
        $status = $this->get('open_orchestra_model.repository.status')->find($statusId);

        $url = $this->generateUrl('open_orchestra_backoffice_status_form', array('statusId' => $statusId));
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.status.success');

        return $this->formHandler($url, $request, $status, $message, StatusEvents::STATUS_UPDATE);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/status/new", name="open_orchestra_backoffice_status_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $statusClass = $this->container->getParameter('open_orchestra_model.document.status.class');
        /** @var StatusInterface $status */
        $status = new $statusClass();

        $url = $this->generateUrl('open_orchestra_backoffice_status_new');
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.status.creation');

        return $this->formHandler($url, $request, $status, $message, StatusEvents::STATUS_CREATE);
    }

    /**
     * @param String          $url
     * @param Request         $request
     * @param StatusInterface $status
     * @param String          $message
     * @param String          $events
     *
     * @return Response
     */
    protected function formHandler($url, Request $request, StatusInterface $status, $message, $events)
    {
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

            $this->dispatchEvent($events, new StatusEvent($status));

            $this->get('session')->getFlashBag()->add(
                'success',
                $message
            );
        }

        return $this->render('OpenOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
