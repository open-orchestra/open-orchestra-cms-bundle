<?php

namespace OpenOrchestra\ApiBundle\OAuth2;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\AuthorizationNonSupportedHttpException;
use OpenOrchestra\ApiBundle\OAuth2\Strategy\StrategyInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthorizationServer
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
class AuthorizationServer
{
    protected $strategies;

    /**
     * @param StrategyInterface $strategy
     */
    public function addStrategy(StrategyInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws AuthorizationNonSupportedHttpException
     */
    public function requestToken(Request $request)
    {
        /** @var StrategyInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->supportRequestToken($request)) {
                return $strategy->requestToken($request);
            }
        }

        throw new AuthorizationNonSupportedHttpException();
    }
}
