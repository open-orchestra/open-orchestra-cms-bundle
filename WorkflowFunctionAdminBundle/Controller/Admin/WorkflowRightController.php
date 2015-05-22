<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Controller\Admin;

use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\WorkflowFunction\Event\WorkflowRightEvent;
use OpenOrchestra\WorkflowFunction\WorkflowRightEvents;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\WorkflowFunction\Model\WorkflowRightInterface;
use OpenOrchestra\WorkflowFunctionBundle\Document\Reference;

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
        $contentTypes = $this->get('open_orchestra_model.repository.content_type')->findAllByDeletedInLastVersion();
        $reference = new Reference();
        $reference->setId(WorkflowRightInterface::NODE);
        $contentTypes[] = $reference;

        $workflowRight = $this->get('open_orchestra_workflow_function.repository.workflowRight')->findOneByUserId($userId);
        $workflowRight = $this->get('open_orchestra_workflow_function.manager.workflow_right')->clean($contentTypes, $workflowRight);
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
