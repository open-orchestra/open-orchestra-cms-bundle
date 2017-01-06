<?php

namespace OpenOrchestra\WorkflowAdminBundle\Controller\Admin;

use OpenOrchestra\ModelInterface\Event\WorkflowProfileEvent;
use OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface;
use OpenOrchestra\ModelInterface\WorkflowProfileEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;

/**
 * Class WorkflowProfileController
 */
class WorkflowProfileController extends AbstractAdminController
{
    /**
     * @param Request $request
     *
     * @Config\Route("/workflow-profile/new", name="open_orchestra_workflow_admin_workflow_profile_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $workflowProfileClass = $this->container->getParameter('open_orchestra_model.document.workflow_profile.class');
        /** @var WorkflowProfileInterface $workflowProfile */
        $workflowProfile = new $workflowProfileClass();
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, WorkflowProfileInterface::ENTITY_TYPE);

        $form = $this->createForm('oo_workflow_profile', $workflowProfile, array(
            'action' => $this->generateUrl('open_orchestra_workflow_admin_workflow_profile_new'),
            'method' => 'POST',
        ));
        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_workflow_admin.form.workflow_profile.creation');
        if ($this->handleForm($form, $message, $workflowProfile)) {
            $this->dispatchEvent(WorkflowProfileEvents::WORKFLOW_PROFILE_CREATE, new WorkflowProfileEvent($workflowProfile));
            $response = new Response(
                '',
                Response::HTTP_CREATED,
                array('Content-type' => 'text/html; charset=utf-8', 'workflowProfileId' => $workflowProfile->getId())
            );

            return $response;
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param int     $workflowProfileId
     *
     * @Config\Route("/workflow-profile/form/{workflowProfileId}", name="open_orchestra_workflow_admin_workflow_profile_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $workflowProfileId)
    {
        $workflowProfile = $this->get('open_orchestra_model.repository.workflow_profile')->find($workflowProfileId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $workflowProfile);

        $form = $this->createForm('oo_workflow_profile', $workflowProfile, array(
                'action' => $this->generateUrl('open_orchestra_workflow_admin_workflow_profile_form', array(
                    'workflowProfileId' => $workflowProfileId,
                )))
        );

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_workflow_admin.form.workflow_profile.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(WorkflowProfileEvents::WORKFLOW_PROFILE_UPDATE, new WorkflowProfileEvent($workflowProfile));
        }

        return $this->renderAdminForm($form);
    }
}
