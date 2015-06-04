<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Controller\Api;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\WorkflowFunction\Event\WorkflowFunctionEvent;
use OpenOrchestra\WorkflowFunction\WorkflowFunctionEvents;
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
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_workflow_functions_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_WORKFLOWFUNCTION')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $columns = $request->get('columns');
        $search = $request->get('search');
        $search = (null !== $search && isset($search['value'])) ? $search['value'] : null;
        $order = $request->get('order');
        $skip = $request->get('start');
        $skip = (null !== $skip) ? (int)$skip : null;
        $limit = $request->get('length');
        $limit = (null !== $limit) ? (int)$limit : null;

        $columnsNameToEntityAttribute = array(
            'site_id' => array('key' => 'siteId'),
            'name'    => array('key' => 'name'),
        );

        $repository =  $this->get('open_orchestra_workflow_function.repository.workflow_function');

        $workflowFunctionCollection = $repository->findForPaginateAndSearch($columnsNameToEntityAttribute, $columns, $search, $order, $skip, $limit);
        $recordsTotal = $repository->count();
        $recordsFiltered = $repository->countFilterSearch($columnsNameToEntityAttribute, $columns, $search);

        $facade = $this->get('open_orchestra_api.transformer_manager')->get('workflow_function_collection')->transform($workflowFunctionCollection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
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
