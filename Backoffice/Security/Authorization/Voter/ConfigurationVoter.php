<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Model\GroupInterface;

/**
 * Class ConfigurationVoter
 *
 * Voter checking rights on configuration management
 */
class ConfigurationVoter extends AbstractPerimeterVoter
{
    protected function getSupportedClasses()
    {
        return array(
            'OpenOrchestra\UserBundle\Model\UserInterface',
            'OpenOrchestra\Backoffice\Model\GroupInterface',
            'OpenOrchestra\ModelInterface\Model\SiteInterface'
        );
    }

    /**
     * A user can act on a site configuration item (user, group or site)
     * if he has a SITE_ADMIN role on the $subject's site
     *
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if ($this->isSuperAdmin($user)) {
            return true;
        }

        if (!$user->hasRole(ContributionRoleInterface::SITE_ADMIN)) {
            return false;
        }

        if (ContributionActionInterface::READ == $attribute) {
            return true;
        }

        if ($subject instanceof UserInterface) {
            return $this->voteForUser($subject, $user);
        }

        if ($subject instanceof GroupInterface || $subject instanceof SiteInterface) {
            return $this->isSubjectInAllowedPerimeter($subject->getSite()->getSiteId(), $user, SiteInterface::ENTITY_TYPE);
        }

        return false;
    }

    /**
     * Vote to see if $user can act on the user $subject
     * $user can do it if $subject is in a group depending on $user
     *
     * @param UserInterface $subject
     * @param UserInterface $user
     *
     * @return bool
     */
    protected function voteForUser(UserInterface $subject, UserInterface $user)
    {
        foreach ($subject->getGroups() as $group) {
            if ($this->isSubjectInAllowedPerimeter($group->getSite()->getSiteId(), $user, SiteInterface::ENTITY_TYPE)) {
                return true;
            }
        }

        return false;
    }
}
