<?php

namespace OpenOrchestra\WorkflowAdminBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Event\StatusEvent;
use OpenOrchestra\ModelInterface\StatusEvents;

/**
 * Class ParametersController
 */
class ParametersController extends AbstractAdminController
{
    /**
     * @param Request $request
     *
     * @Config\Route("/workflow-parameters/form", name="open_orchestra_workflow_admin_parameters_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, StatusInterface::ENTITY_TYPE);

        $statusCollection = $this->get('open_orchestra_model.repository.status')->findNotOutOfWorkflow();
            $data = array(
            'statuses' => $statusCollection,
            'labels'   => array(
                $this->get('translator')->trans('open_orchestra_workflow_admin.status.initial_state'          , array(), 'interface'),
                $this->get('translator')->trans('open_orchestra_workflow_admin.status.published_state'        , array(), 'interface'),
                $this->get('translator')->trans('open_orchestra_workflow_admin.status.auto_publish_from_state', array(), 'interface'),
                $this->get('translator')->trans('open_orchestra_workflow_admin.status.auto_unpublish_to_state', array(), 'interface'),
                $this->get('translator')->trans('open_orchestra_workflow_admin.status.translation_state'      , array(), 'interface')
            )
        );

        $form = $this->createForm('oo_workflow_parameters', $data, array(
            'action' => $this->generateUrl('open_orchestra_workflow_admin_parameters_form')
        ));

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.parameters.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(StatusEvents::STATUS_UPDATE, new StatusEvent($status));
        }

        return $this->renderAdminForm($form);
    }
}
