<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
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
     * @Config\Route("", name="open_orchestra_api_api_client_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_API_CLIENT')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $clientCollection = $this->get('open_orchestra_api.repository.api_client')->findAll();

        return $this->get('open_orchestra_api.transformer_manager')
                    ->get('api_client_collection')
                    ->transform($clientCollection);
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
