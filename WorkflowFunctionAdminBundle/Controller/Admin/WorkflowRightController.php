<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Controller\Admin;

use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\WorkflowFunction\Event\WorkflowRightEvent;
use OpenOrchestra\WorkflowFunction\WorkflowRightEvents;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\WorkflowFunction\Model\WorkflowRightInterface;

/**
 * Class WorkflowRightController
 *
 * @Config\Route("workflow-right")
 */
class WorkflowRightController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $userId
     *
     * @Config\Route("/form/{userId}", name="open_orchestra_backoffice_workflow_right_form")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_USER')")
     *
     * @return Response
     */
    public function formAction(Request $request, $userId)
    {
        $user = $this->get('open_orchestra_user.repository.user')->find($userId);
        $workflowRight = null;
        if (null !== $user) {
            $workflowRight = $this->get('open_orchestra_workflow_function.repository.workflowRight')->findOneByUser($user);
        }
        $workflowRight = $this->get('open_orchestra_workflow_function_admin.manager.workflow_right')->clean($workflowRight);
        $url = $this->generateUrl('open_orchestra_backoffice_workflow_right_form', array('userId' => $userId));

        return $this->generateForm($request, $workflowRight, $url, WorkflowRightEvents::WORKFLOWRIGHT_UPDATE);
    }

    /**
     * @param Request                   $request
     * @param WorkflowRightInterface    $workflowRight
     * @param string                    $url
     * @param string                    $workflowRightEvents
     *
     * @return Response
     */
    protected function generateForm(Request $request, WorkflowRightInterface $workflowRight, $url, $workflowRightEvents)
    {
        $form = $this->createForm('workflow_right', $workflowRight, array('action' => $url));
        $form->handleRequest($request);
        $this->handleForm(
            $form,
            $this->get('translator')->trans('open_orchestra_workflow_function.form.workflow_right.success'),
            $workflowRight
        );

        $this->dispatchEvent($workflowRightEvents, new WorkflowRightEvent($workflowRight));

        return $this->renderAdminForm($form);
    }
}
