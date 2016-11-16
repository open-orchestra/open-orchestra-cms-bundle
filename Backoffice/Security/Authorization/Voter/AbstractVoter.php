<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;

/**
 * Class AbstractVoter
 */
abstract class AbstractVoter extends Voter
{
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
