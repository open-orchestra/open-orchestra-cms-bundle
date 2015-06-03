<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\StatusEvent;
use OpenOrchestra\ModelInterface\StatusEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class StatusController
 *
 * @Config\Route("status")
 */
class StatusController extends BaseController
{
    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_status_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_STATUS')")
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
            'label'     => array('key' => 'name'),
            'published' => array('key' => 'published','type' => 'boolean'),
            'initial' => array('key' => 'initial','type' => 'boolean'),
            'display_color' => array('key' => 'displayColor'),
        );

        $repository = $this->get('open_orchestra_model.repository.status');

        $statusCollection = $repository->findForPaginateAndSearch($columnsNameToEntityAttribute, $columns, $search, $order, $skip, $limit);
        $recordsTotal = $repository->count();
        $recordsFiltered = $repository->countFilterSearch($columnsNameToEntityAttribute, $columns, $search);

        $facade = $this->get('open_orchestra_api.transformer_manager')->get('status_collection')->transform($statusCollection);
        $facade->setRecordsTotal($recordsTotal);
        $facade->setRecordsFiltered($recordsFiltered);

        return $facade;
    }

    /**
     * @param int $statusId
     *
     * @Config\Route("/{statusId}/delete", name="open_orchestra_api_status_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_STATUS')")
     *
     * @return Response
     */
    public function deleteAction($statusId)
    {
        $status = $this->get('open_orchestra_model.repository.status')->find($statusId);
        $this->get('event_dispatcher')->dispatch(StatusEvents::STATUS_DELETE, new StatusEvent($status));
        $this->get('doctrine.odm.mongodb.document_manager')->remove($status);
        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response('', 200);
    }
}
