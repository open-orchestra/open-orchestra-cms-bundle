<?php

namespace OpenOrchestra\BackofficeBundle\StrategyManager;

use OpenOrchestra\Backoffice\AuthorizeStatusChange\AuthorizeStatusChangeInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

/**
 * Class AuthorizeStatusChangeManager
 */
class AuthorizeStatusChangeManager
{
    protected $strategies = array();
    protected $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
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
        if ($this->authorizationChecker->isGranted(ContributionRoleInterface::PLATFORM_ADMIN)) {
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
}
