<?php

namespace OpenOrchestra\ApiBundle\OAuth2\Strategy;

use OpenOrchestra\ApiBundle\Facade\OAuth2\AccessTokenFacade;
use OpenOrchestra\UserBundle\Document\AccessToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ClientCredentialsGrantStrategy
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
class ClientCredentialsGrantStrategy extends AbstractStrategy
{
    /**
     * @param Request $request
     *
     * @return boolean
     */
    public function supportRequestToken(Request $request)
    {
        $clientExist = $request->getUser() && $request->getPassword();
        $oauthParams = $request->get('grant_type') === 'client_credentials';

        return $oauthParams && $clientExist;
    }

    /**
     * @param Request $request [description]
     *
     * @return Response
     */
    public function requestToken(Request $request)
    {
        $client = $this->getClient($request);

        /** @var TokenInterface $accessToken */
        $accessToken = $this->accessTokenRepository->findOneByClientWithoutUser($client);

        if (is_null($accessToken) || $accessToken->isBlocked() || $accessToken->isExpired()) {
            // Create/Validate AccessToken
            $tokenClass = $this->tokenClass;
            $accessToken = $tokenClass::create(null, $client);
            if (!$accessToken->isValid($this->validator)) {
                return Response::create($this->serializer->serialize($accessToken->getViolations(), 'json'), 200, array())->prepare($request);
            }

            $this->accessTokenRepository->save($accessToken);
        }

        $tokenFacade = new AccessTokenFacade();
        $tokenFacade->accessToken   = $accessToken->getCode();
        $tokenFacade->expiresIn     = $accessToken->getExpiredAt();

        return Response::create($this->serializer->serialize($tokenFacade, 'json'), 200, array('Content-Type' => 'application/json'))->prepare($request);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'client_credentials';
    }
}
