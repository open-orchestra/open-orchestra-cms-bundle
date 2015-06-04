<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class ApiClientController
 *
 * @Config\Route("api-client")
 */
class ApiClientController extends BaseController
{
    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_api_client_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_API_CLIENT')")
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
            'name' => array('key' => 'name'),
            'trusted' => array('key' => 'trusted', 'type' => 'boolean'),
        );

        $repository = $this->get('open_orchestra_api.repository.api_client');

        $clientCollection = $repository->findForPaginateAndSearch($columnsNameToEntityAttribute, $columns, $search, $order, $skip, $limit);
        $recordsTotal = $repository->count();
        $recordsFiltered = $repository->countFilterSearch($columnsNameToEntityAttribute, $columns, $search);

        $facade = $this->get('open_orchestra_api.transformer_manager')->get('api_client_collection')->transform($clientCollection);
        $facade->setRecordsTotal($recordsTotal);
        $facade->setRecordsFiltered($recordsFiltered);

        return $facade;
    }

    /**
     * @param int $apiClientId
     *
     * @Config\Route("/{apiClientId}/delete", name="open_orchestra_api_api_client_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_API_CLIENT')")
     *
     * @return Response
     */
    public function deleteAction($apiClientId)
    {
        $apiClient = $this->get('open_orchestra_api.repository.api_client')->find($apiClientId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->remove($apiClient);
        $dm->flush();

        return new Response('', 200);
    }
}
