<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Class AbstractVoter
 */
abstract class AbstractVoter extends Voter
{

    protected $decisionManager;

    /**
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * @return array
     */
    protected function getSupportedAttributes()
    {
        return array(
            ContributionActionInterface::READ,
            ContributionActionInterface::CREATE,
            ContributionActionInterface::EDIT,
            ContributionActionInterface::DELETE
        );
    }

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, $this->getSupportedAttributes())) {
            return false;
        }

        return $this->supportSubject($subject);
    }

    /**
     * @param mixed $subject
     *
     * @return bool
     */
    abstract protected function supportSubject($subject);

    /**
     * @param mixed $subject
     * @param array $classes
     *
     * @return bool
     */
    protected function supportClasses($subject, array $classes) {
        foreach ($classes as $supportedClass) {
            if ($subject instanceof $supportedClass) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function isSuperAdmin(TokenInterface $token)
    {
        return $this->decisionManager->decide($token, array(ContributionRoleInterface::PLATFORM_ADMIN));
    }

    /**
     * @param TokenInterface $token
     * @param string         $role
     *
     * @return bool
     */
    protected function hasRole(TokenInterface $token, $role)
    {
        $roles = $token->getRoles();
        foreach ($roles as $checkRole) {
            if (($checkRole instanceof RoleInterface && $role === $checkRole->getRole()) ||
                (is_string($checkRole) && $role === $checkRole)
            ) {
                return true;
            }
        }

        return false;
    }
}
