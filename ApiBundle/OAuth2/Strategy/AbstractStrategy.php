<?php

namespace OpenOrchestra\ApiBundle\OAuth2\Strategy;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\BadClientCredentialsHttpException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\ClientBlockedHttpException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\ClientNonTrustedHttpException;
use JMS\Serializer\Serializer;
use OpenOrchestra\UserBundle\Model\ApiClientInterface;
use OpenOrchestra\UserBundle\Repository\AccessTokenRepository;
use OpenOrchestra\UserBundle\Repository\ApiClientRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\LegacyValidator;

/**
 * Class AbstractStrategy
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
abstract class AbstractStrategy implements StrategyInterface
{
    protected $accessTokenRepository;
    protected $apiClientRepository;
    protected $tokenExpiration;
    protected $tokenClass;
    protected $serializer;
    protected $validator;

    /**
     * @param ApiClientRepository   $apiClientRepository
     * @param AccessTokenRepository $accessTokenRepository
     * @param Serializer            $serializer
     * @param LegacyValidator       $validator
     * @param string                $tokenExpiration
     * @param string                $tokenClass
     */
    public function __construct(
        ApiClientRepository $apiClientRepository,
        AccessTokenRepository $accessTokenRepository,
        Serializer $serializer,
        LegacyValidator $validator,
        $tokenExpiration,
        $tokenClass
    )
    {
        $this->apiClientRepository = $apiClientRepository;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->tokenExpiration = $tokenExpiration;
        $this->tokenClass = $tokenClass;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     *
     * @return ApiClientInterface
     * @throws BadClientCredentialsHttpException
     * @throws ClientNonTrustedHttpException
     * @throws ClientBlockedHttpException
     */
    protected function getClient(Request $request)
    {
        $client = $this->apiClientRepository->findOneByKeyAndSecret($request->getUser(), $request->getPassword());
        if (!$client) {
            throw new BadClientCredentialsHttpException();
        } elseif ($client->isBlocked()) {
            throw new ClientBlockedHttpException();
        } elseif (!$client->isTrusted()) {
            throw new ClientNonTrustedHttpException();
        }

        return $client;
    }
}
