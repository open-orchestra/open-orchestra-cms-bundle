<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\Annotation as Api;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

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
        $clientCollection = $this->get('open_orchestra_user.repository.api_client')->findAll();

        return $this->get('open_orchestra_api.transformer_manager')
                    ->get('api_client_collection')
                    ->transform($clientCollection);
    }
}
