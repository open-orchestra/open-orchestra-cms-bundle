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
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_STATUS')")
     *
     * @return Response
     */
    public function formAction(Request $request, $statusId)
    {
        $status = $this->get('open_orchestra_model.repository.status')->find($statusId);

        $form = $this->createForm('oo_status', $status, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_status_form', array(
                'statusId' => $statusId,
            )))
        );

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.status.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(StatusEvents::STATUS_UPDATE, new StatusEvent($status));
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/status/new", name="open_orchestra_backoffice_status_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_CREATE_STATUS')")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $statusClass = $this->container->getParameter('open_orchestra_model.document.status.class');
        /** @var StatusInterface $status */
        $status = new $statusClass();

        $form = $this->createForm('oo_status', $status, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_status_new'),
            'method' => 'POST',
        ));

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.status.creation');

        if ($this->handleForm($form, $message, $status)) {
            $this->dispatchEvent(StatusEvents::STATUS_CREATE, new StatusEvent($status));
            $response = new Response('', Response::HTTP_CREATED, array('Content-type' => 'text/html; charset=utf-8'));

            return $this->render('BraincraftedBootstrapBundle::flash.html.twig', array(), $response);
        }

        return $this->renderAdminForm($form);
    }
}
