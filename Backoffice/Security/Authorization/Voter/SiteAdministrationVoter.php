<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;
use OpenOrchestra\LogBundle\Model\LogInterface;

/**
 * Class SiteAdministrationVoter
 *
 * Voter checking rights on site management
 */
class SiteAdministrationVoter extends AbstractPerimeterVoter
{
    /**
     * @return array
     */
    protected function getSupportedClasses()
    {
        return array(
            'OpenOrchestra\ModelInterface\Model\SiteInterface',
            'OpenOrchestra\ModelInterface\Model\RedirectionInterface',
            'OpenOrchestra\LogBundle\Model\LogInterface',
            'OpenOrchestra\UserBundle\Model\UserInterface',
            'OpenOrchestra\Backoffice\Model\GroupInterface'
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

        if ($subject instanceof LogInterface) {
            return true;
        }

        if ($subject instanceof RedirectionInterface || $subject instanceof SiteInterface) {
            return $this->canActOnSite($subject->getSiteId(), $user);
        }

        if ($subject instanceof UserInterface) {
            return $this->voteForUser($subject, $user);
        }

        if ($subject instanceof GroupInterface) {
            return $this->canActOnSite($subject->getSite()->getSiteId(), $user);
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
            if ($this->canActOnSite($group->getSite()->getSiteId(), $user)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the $user has $siteId in his Sites Perimeter
     *
     * @param string        $siteId
     * @param UserInterface $user
     *
     * @return boolean
     */
    protected function canActOnSite($siteId, UserInterface $user)
    {
        return $this->isSubjectInAllowedPerimeter($siteId, $user, SiteInterface::ENTITY_TYPE);
    }
}
