<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Controller\Api;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\WorkflowFunction\Event\WorkflowFunctionEvent;
use OpenOrchestra\WorkflowFunction\WorkflowFunctionEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;

/**
 * Class WorkflowFunctionController
 *
 * @Config\Route("workflowfunction")
 */
class WorkflowFunctionController extends Controller
{
    /**
     * @param string $workflowFunctionId
     *
     * @Config\Route("/{workflowFunctionId}", name="open_orchestra_api_workflowfunction_show")
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
        $workflowFunction = $this->get('open_orchestra_workflowfunction.repository.workflowfunction')->find($workflowFunctionId);

        return $this->get('open_orchestra_api.transformer_manager')->get('workflowfunction')->transform($workflowFunction);
    }

    /**
     * @Config\Route("", name="open_orchestra_api_workflowfunctions_list")
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
        $workflowFunctionCollection = $this->get('open_orchestra_workflowfunction.repository.workflowfunction')->findAll();

        return $this->get('open_orchestra_api.transformer_manager')->get('workflowfunction_collection')->transform($workflowFunctionCollection);
    }

    /**
     * @param string $workflowFunctionId
     *
     * @Config\Route("/{workflowFunctionId}/delete", name="open_orchestra_api_workflowfunction_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_WORKFLOWFUNCTION')")
     *
     * @return Response
     */
    public function deleteAction($workflowFunctionId)
    {
        $workflowFunction = $this->get('open_orchestra_workflowfunction.repository.workflowfunction')->find($workflowFunctionId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $this->dispatchEvent(WorkflowFunctionEvents::WORKFLOWFUNCTION_DELETE, new WorkflowFunctionEvent($workflowFunction));
        $dm->remove($workflowFunction);
        $dm->flush();

        return new Response('', 200);
    }
}
