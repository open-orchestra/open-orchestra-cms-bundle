<?php

namespace OpenOrchestra\ApiBundle\Security\Firewall;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\ClientAccessDeniedHttpException;
use OpenOrchestra\ApiBundle\Security\Authentication\Token\OAuth2Token;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * Class OAuth2Listener
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
class OAuth2Listener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;

    /**
     * @param SecurityContextInterface       $securityContext
     * @param AuthenticationManagerInterface $authenticationManager
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
    {
        $this->securityContext       = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws ClientAccessDeniedHttpException
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($this->securityContext->getToken() instanceof TokenInterface && $this->securityContext->getToken()->isAuthenticated()) {
            return;
        }

        if (!($accessToken = $request->get('access_token'))) {
            throw new ClientAccessDeniedHttpException();
        }

        $token = $this->authenticationManager->authenticate(OAuth2Token::create($accessToken));
        $this->securityContext->setToken($token);
    }
}
