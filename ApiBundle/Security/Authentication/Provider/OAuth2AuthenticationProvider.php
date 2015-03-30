<?php

namespace OpenOrchestra\ApiBundle\Security\Authentication\Provider;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\TokenBlockedHttpException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\UserNotFoundHttpException;
use OpenOrchestra\ApiBundle\Security\Authentication\Token\OAuth2Token;
use OpenOrchestra\UserBundle\Repository\AccessTokenRepository;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class OAuth2AuthenticationProvider
 */
class OAuth2AuthenticationProvider implements AuthenticationProviderInterface
{
    protected $accessTokenRepository;

    /**
     * @param AccessTokenRepository $accessTokenRepository
     */
    public function __construct(AccessTokenRepository $accessTokenRepository)
    {
        $this->accessTokenRepository = $accessTokenRepository;
    }

    /**
     * Attempts to authenticate a TokenInterface object.
     *
     * @param TokenInterface $token The TokenInterface instance to authenticate
     *
     * @throws TokenBlockedHttpException
     * @throws UserNotFoundHttpException
     * @return TokenInterface An authenticated TokenInterface instance, never null
     */
    public function authenticate(TokenInterface $token)
    {
        $accessToken = $token->getAccessToken();
        $accessTokenEntity = $this->accessTokenRepository->findOneByCode($accessToken);
        if (is_null($accessTokenEntity) || $accessTokenEntity->isBlocked()) {
            throw new TokenBlockedHttpException();
        }

        $authenticatedToken = OAuth2Token::createFromAccessTokenEntity($accessTokenEntity);

        return $authenticatedToken;
    }

    /**
     * Checks whether this provider supports the given token.
     *
     * @param TokenInterface $token A TokenInterface instance
     *
     * @return Boolean true if the implementation supports the Token, false otherwise
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof OAuth2Token;
    }

}
