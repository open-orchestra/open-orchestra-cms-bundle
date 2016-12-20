<?php

namespace OpenOrchestra\WorkflowAdminBundle\Controller\Admin;

use OpenOrchestra\ModelInterface\Event\StatusEvent;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\StatusEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;

/**
 * Class StatusController
 */
class StatusController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param int     $statusId
     *
     * @Config\Route("/status/form/{statusId}", name="open_orchestra_workflow_admin_status_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $statusId)
    {
        $status = $this->get('open_orchestra_model.repository.status')->find($statusId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $status);

        $form = $this->createForm('oo_status', $status, array(
            'action' => $this->generateUrl('open_orchestra_workflow_admin_status_form', array(
                'statusId' => $statusId,
            )))
        );

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_workflow_admin.form.status.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(StatusEvents::STATUS_UPDATE, new StatusEvent($status));
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/status/new", name="open_orchestra_workflow_admin_status_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $statusClass = $this->getParameter('open_orchestra_model.document.status.class');
        /** @var StatusInterface $status */
        $status = new $statusClass();
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, $status);

        $form = $this->createForm('oo_status', $status, array(
            'action' => $this->generateUrl('open_orchestra_workflow_admin_status_new'),
            'method' => 'POST',
        ));

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_workflow_admin.form.status.creation');

        if ($this->handleForm($form, $message, $status)) {
            $this->dispatchEvent(StatusEvents::STATUS_CREATE, new StatusEvent($status));
            $response = new Response(
                $message,
                Response::HTTP_CREATED,
                array('Content-type' => 'text/html; charset=utf-8', 'statusId' => $status->getId())
            );

            return $response;
        }

        return $this->renderAdminForm($form);
    }
}
