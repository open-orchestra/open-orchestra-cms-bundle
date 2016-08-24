<?php

namespace OpenOrchestra\BackofficeBundle\StrategyManager;

use OpenOrchestra\Backoffice\AuthorizeStatusChange\AuthorizeStatusChangeInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

/**
 * Class AuthorizeStatusChangeManager
 */
class AuthorizeStatusChangeManager
{
    protected $strategies = array();

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param AuthorizeStatusChangeInterface $strategy
     */
    public function addStrategy(AuthorizeStatusChangeInterface $strategy)
    {
        $this->strategies[$strategy->getName()] = $strategy;
    }

    /**
     * @param StatusableInterface $document
     * @param StatusInterface     $toStatus
     *
     * @return bool
     */
    public function isGranted(StatusableInterface $document, StatusInterface $toStatus)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        /** @var AuthorizeStatusChangeInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if (!$strategy->isGranted($document, $toStatus)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function isSuperAdmin()
    {
        if (null === ($token = $this->tokenStorage->getToken())) {
            throw new AuthenticationCredentialsNotFoundException('The token storage contains no authentication token. One possible reason may be that there is no firewall configured for this URL.');
        }

        if (($user = $token->getUser()) instanceof UserInterface && $user->isSuperAdmin()) {
            return true;
        }

        return false;
    }
}
