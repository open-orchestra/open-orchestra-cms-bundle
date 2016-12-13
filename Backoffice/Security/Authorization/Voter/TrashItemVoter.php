<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class TrashItemVoter
 *
 * Voter checking rights on trash management
 */
class TrashItemVoter extends AbstractVoter
{
    /**
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supportSubject($subject)
    {
        return $this->supportClasses(
            $subject,
            array('OpenOrchestra\ModelInterface\Model\TrashItemInterface')
        );
    }

    /**
     * @return array
     */
    protected function getSupportedAttributes()
    {
        return array(
            ContributionActionInterface::READ,
            ContributionActionInterface::TRASH_RESTORE,
            ContributionActionInterface::TRASH_PURGE
        );
    }

    /**
     * Everyone can read the trash item
     * but you can only purge or restore items with the matching role
     *
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->isSuperAdmin($token)) {
            return true;
        }

        if (ContributionActionInterface::READ == $attribute) {
            return true;
        }

        $actionMapping = array(
            ContributionActionInterface::TRASH_RESTORE => ContributionRoleInterface::TRASH_RESTORER,
            ContributionActionInterface::TRASH_PURGE   => ContributionRoleInterface::TRASH_SUPRESSOR
        );

        return $this->hasRole($token, $actionMapping[$attribute]);
    }
}
