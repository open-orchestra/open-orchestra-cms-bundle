<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Controller\Api;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\WorkflowFunction\Event\WorkflowFunctionEvent;
use OpenOrchestra\WorkflowFunction\WorkflowFunctionEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class WorkflowFunctionController
 *
 * @Config\Route("workflow-function")
 */
class WorkflowFunctionController extends BaseController
{
    /**
     * @param string $workflowFunctionId
     *
     * @Config\Route("/{workflowFunctionId}", name="open_orchestra_api_workflow_function_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_WORKFLOWFUNCTION')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($workflowFunctionId)
    {
        $workflowFunction = $this->get('open_orchestra_workflow_function.repository.workflow_function')->find($workflowFunctionId);

        return $this->get('open_orchestra_api.transformer_manager')->get('workflow_function')->transform($workflowFunction);
    }

    /**
     * @Config\Route("", name="open_orchestra_api_workflow_functions_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_WORKFLOWFUNCTION')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $workflowFunctionCollection = $this->get('open_orchestra_workflow_function.repository.workflow_function')->findAll();

        return $this->get('open_orchestra_api.transformer_manager')->get('workflow_function_collection')->transform($workflowFunctionCollection);
    }

    /**
     * @param string $workflowFunctionId
     *
     * @Config\Route("/{workflowFunctionId}/delete", name="open_orchestra_api_workflow_function_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_WORKFLOWFUNCTION')")
     *
     * @return Response
     */
    public function deleteAction($workflowFunctionId)
    {
        $workflowFunction = $this->get('open_orchestra_workflow_function.repository.workflow_function')->find($workflowFunctionId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $this->dispatchEvent(WorkflowFunctionEvents::WORKFLOWFUNCTION_DELETE, new WorkflowFunctionEvent($workflowFunction));
        $dm->remove($workflowFunction);
        $dm->flush();

        return new Response('', 200);
    }
}
