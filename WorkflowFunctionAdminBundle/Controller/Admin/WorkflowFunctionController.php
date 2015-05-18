<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Controller\Admin;

use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\WorkflowFunction\Event\WorkflowFunctionEvent;
use OpenOrchestra\WorkflowFunction\WorkflowFunctionEvents;
use OpenOrchestra\WorkflowFunction\Model\WorkflowFunctionInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WorkflowFunctionController
 *
 * @Config\Route("workflowfunction")
 */
class WorkflowFunctionController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $workflowFunctionId
     *
     * @Config\Route("/form/{workflowFunctionId}", name="open_orchestra_backoffice_workflowfunction_form")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_WORKFLOWFUNCTION')")
     *
     * @return Response
     */
    public function formAction(Request $request, $workflowFunctionId)
    {
        $workflowFunctionRepository = $this->container->get('open_orchestra_workflowfunction.repository.workflowFunction');
        $workflowFunction = $workflowFunctionRepository->find($workflowFunctionId);

        $url = $this->generateUrl('open_orchestra_backoffice_workflowfunction_form', array('workflowFunctionId' => $workflowFunctionId));

        return $this->generateForm($request, $workflowFunction, $url, WorkflowFunctionEvents::WORKFLOWFUNCTION_UPDATE);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/new", name="open_orchestra_backoffice_workflowfunction_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_WORKFLOWFUNCTION')")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $workflowFunctionClass = $this->container->getParameter('open_orchestra_workflowfunction.document.workflowFunction.class');
        $workflowFunction = new $workflowFunctionClass();

        $url = $this->generateUrl('open_orchestra_backoffice_workflowfunction_new');

        return $this->generateForm($request, $workflowFunction, $url, WorkflowFunctionEvents::WORKFLOWFUNCTION_CREATE);
    }

    /**
     * @param Request                   $request
     * @param WorkflowFunctionInterface $workflowFunction
     * @param string                    $url
     * @param string                    $workflowFunctionEvents
     *
     * @return Response
     */
    protected function generateForm(Request $request, WorkflowFunctionInterface $workflowFunction, $url, $workflowFunctionEvents)
    {
        $form = $this->createForm('workflowfunction', $workflowFunction, array('action' => $url));
        $form->handleRequest($request);
        $this->handleForm(
            $form,
            $this->get('translator')->trans('open_orchestra_workflowfunction.form.workflowfunction.success'),
            $workflowFunction
        );

        $this->dispatchEvent($workflowFunctionEvents, new WorkflowFunctionEvent($workflowFunction));

        return $this->renderAdminForm($form);
    }
}
