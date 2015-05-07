<?php

namespace OpenOrchestra\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class AuthorizationController
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
class AuthorizationController extends BaseController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function accessTokenAction(Request $request)
    {
        return $this->get('open_orchestra_api.oauth2.authorization_server')->requestToken($request);
    }
}
