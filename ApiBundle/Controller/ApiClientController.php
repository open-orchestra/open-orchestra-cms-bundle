<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
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
    use HandleRequestDataTable;

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
        $columnsNameToEntityAttribute = array(
            'name' => array('key' => 'name'),
            'trusted' => array('key' => 'trusted', 'type' => 'boolean'),
        );
        $repository = $this->get('open_orchestra_api.repository.api_client');
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('api_client_collection');

        return $this->handleRequestDataTable($request, $repository, $columnsNameToEntityAttribute, $collectionTransformer);
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
        $dm = $this->get('document_manager');
        $dm->remove($apiClient);
        $dm->flush();

        return new Response('', 200);
    }
}
