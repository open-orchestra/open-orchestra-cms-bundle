<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\Event\StatusEvent;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\StatusEvents;
use Symfony\Component\Form\Form;
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
     * @Config\Security("has_role('ROLE_ACCESS_STATUS')")
     *
     * @return Response
     */
    public function formAction(Request $request, $statusId)
    {
        $status = $this->get('open_orchestra_model.repository.status')->find($statusId);

        $url = $this->generateUrl('open_orchestra_backoffice_status_form', array('statusId' => $statusId));
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.status.success');

        $form = $this->generateForm($status, $url);
        $form->handleRequest($request);

        $this->handleForm($form, $message, $status);

        $this->dispatchEvent(StatusEvents::STATUS_UPDATE, new StatusEvent($status));

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/status/new", name="open_orchestra_backoffice_status_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_STATUS')")
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

        $form = $this->generateForm($status, $url);
        $form->handleRequest($request);

        if ($this->handleForm($form, $message, $status)) {
            $url = $this->generateUrl('open_orchestra_backoffice_status_form', array('statusId' => $status->getId()));
            $this->dispatchEvent(StatusEvents::STATUS_CREATE, new StatusEvent($status));

            return $this->redirect($url);
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param StatusInterface $status
     * @param string          $url
     *
     * @return Form
     */
    protected function generateForm(StatusInterface $status, $url)
    {
        $form = $this->createForm(
            'status',
            $status,
            array(
                'action' => $url,
            )
        );

        return $form;
    }
}
