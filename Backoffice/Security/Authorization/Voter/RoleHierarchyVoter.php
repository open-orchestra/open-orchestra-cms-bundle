<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter as BaseRoleHierarchyVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class RoleHierarchyVoter
 */
class RoleHierarchyVoter extends BaseRoleHierarchyVoter
{
    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (($user = $token->getUser()) instanceof UserInterface && $user->isSuperAdmin()) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return parent::vote($token, $object, $attributes);
    }

}
