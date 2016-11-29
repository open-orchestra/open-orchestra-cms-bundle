<?php

namespace OpenOrchestra\WorkflowAdminBundle\Controller\Admin;

use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\Workflow\Event\WorkflowFunctionEvent;
use OpenOrchestra\Workflow\WorkflowFunctionEvents;
use OpenOrchestra\ModelInterface\Model\WorkflowFunctionInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

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
     * @return Response
     */
    public function formAction(Request $request, $workflowFunctionId)
    {
        $workflowFunctionRepository = $this->get('open_orchestra_model.repository.workflow_function');
        $workflowFunction = $workflowFunctionRepository->find($workflowFunctionId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $workflowFunction);

        $url = $this->generateUrl('open_orchestra_backoffice_workflow_function_form', array('workflowFunctionId' => $workflowFunctionId));
        $message = $this->get('translator')->trans('open_orchestra_workflow_admin.form.workflow_function.success');

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
     * @return Response
     */
    public function newAction(Request $request)
    {
        $workflowFunctionClass = $this->getParameter('open_orchestra_model.document.workflow_function.class');
        $workflowFunction = new $workflowFunctionClass();
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, $workflowFunction);

        $url = $this->generateUrl('open_orchestra_backoffice_workflow_function_new');
        $message = $this->get('translator')->trans('open_orchestra_workflow_admin.form.workflow_function.success');

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
            'oo_workflow_function',
            $workflowFunction,
            array('action' => $url)
        );

        return $form;
    }
}
