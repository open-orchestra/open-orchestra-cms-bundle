<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;

/**
 * Class AbstractVoter
 */
abstract class AbstractVoter extends Voter
{
    /**
     * Return the list of supported classes
     *
     * @return array
     */
    abstract protected function getSupportedClasses();

    /**
     * @return array
     */
    protected function getSupportedAttributes()
    {
        return array(
            ContributionActionInterface::READ,
            ContributionActionInterface::ADD,
            ContributionActionInterface::EDIT,
            ContributionActionInterface::DELETE
        );
    }

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, $this->getSupportedAttributes())) {
            return false;
        }

        foreach ($this->getSupportedClasses() as $supportedClass) {
            if ($subject instanceof $supportedClass) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param UserInterface|string $user
     *
     * @return bool
     */
    protected function isSuperAdmin($user = null)
    {
        return ($user instanceof UserInterface
            && ($user->hasRole(ContributionRoleInterface::DEVELOPER) || $user->hasRole(ContributionRoleInterface::PLATFORM_ADMIN))
        );
    }
}
