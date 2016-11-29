<?php

namespace OpenOrchestra\WorkflowAdminBundle\Controller\Admin;

use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\Workflow\Event\WorkflowRightEvent;
use OpenOrchestra\Workflow\WorkflowRightEvents;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\ModelInterface\Model\WorkflowRightInterface;

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
     * @return Response
     */
    public function formAction(Request $request, $userId)
    {
        $workflowRight = $this->get('open_orchestra_workflow_admin.manager.workflow_right')->loadOrGenerateByUser($userId);

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
        $form = $this->createForm('oo_workflow_right', $workflowRight, array('action' => $url));
        $form->handleRequest($request);
        if ($this->handleForm(
            $form,
            $this->get('translator')->trans('open_orchestra_workflow_admin.form.workflow_right.success'),
            $workflowRight)) {
            $this->dispatchEvent($workflowRightEvents, new WorkflowRightEvent($workflowRight));
        }

        $title = 'open_orchestra_workflow_admin.right.title';
        $title = $this->get('translator')->trans($title);

        return $this->renderAdminForm($form, array('title' => $title));
    }
}
