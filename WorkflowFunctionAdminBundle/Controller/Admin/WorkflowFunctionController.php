<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Controller\Admin;

use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\WorkflowFunction\Event\WorkflowFunctionEvent;
use OpenOrchestra\WorkflowFunction\WorkflowFunctionEvents;
use OpenOrchestra\WorkflowFunction\Model\WorkflowFunctionInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;

/**
 * Class WorkflowFunctionController
 *
 * @Config\Route("workflow-function")
 */
class WorkflowFunctionController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $workflowFunctionId
     *
     * @Config\Route("/form/{workflowFunctionId}", name="open_orchestra_backoffice_workflow_function_form")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_WORKFLOWFUNCTION')")
     *
     * @return Response
     */
    public function formAction(Request $request, $workflowFunctionId)
    {
        $workflowFunctionRepository = $this->get('open_orchestra_workflow_function.repository.workflow_function');
        $workflowFunction = $workflowFunctionRepository->find($workflowFunctionId);

        $url = $this->generateUrl('open_orchestra_backoffice_workflow_function_form', array('workflowFunctionId' => $workflowFunctionId));
        $message = $this->get('translator')->trans('open_orchestra_workflow_function_admin.form.workflow_function.success');

        $form = $this->generateForm($workflowFunction, $url);
        $form->handleRequest($request);

        $this->handleForm($form, $message, $workflowFunction);

        $this->dispatchEvent(WorkflowFunctionEvents::WORKFLOWFUNCTION_UPDATE, new WorkflowFunctionEvent($workflowFunction));

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/new", name="open_orchestra_backoffice_workflow_function_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_WORKFLOWFUNCTION')")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $workflowFunctionClass = $this->getParameter('open_orchestra_workflow_function.document.workflow_function.class');
        $workflowFunction = new $workflowFunctionClass();

        $url = $this->generateUrl('open_orchestra_backoffice_workflow_function_new');
        $message = $this->get('translator')->trans('open_orchestra_workflow_function_admin.form.workflow_function.success');

        $form = $this->generateForm($workflowFunction, $url);
        $form->handleRequest($request);

        if ($this->handleForm($form, $message, $workflowFunction)) {
            $url = $this->generateUrl('open_orchestra_backoffice_workflow_function_form', array('workflowFunctionId' => $workflowFunction->getId()));
            $this->dispatchEvent(WorkflowFunctionEvents::WORKFLOWFUNCTION_CREATE, new WorkflowFunctionEvent($workflowFunction));

            return $this->redirect($url);
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param WorkflowFunctionInterface $workflowFunction
     * @param string                    $url
     *
     * @return Form
     */
    protected function generateForm(WorkflowFunctionInterface $workflowFunction, $url)
    {
        $form = $this->createForm(
            'workflow_function',
            $workflowFunction,
            array('action' => $url)
        );

        return $form;
    }
}
