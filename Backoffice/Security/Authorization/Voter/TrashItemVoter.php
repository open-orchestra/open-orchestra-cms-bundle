<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\ModelInterface\Model\TrashItemInterface;
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
    protected $actionMapping = array(
        ContributionActionInterface::READ          => null,
        ContributionActionInterface::TRASH_RESTORE => ContributionRoleInterface::TRASH_RESTORER,
        ContributionActionInterface::TRASH_PURGE   => ContributionRoleInterface::TRASH_SUPRESSOR
    );

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof TrashItemInterface && array_key_exists($attribute, $this->actionMapping);
    }

    /**
     * Evryone can read the trash item
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
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        if (ContributionActionInterface::READ == $attribute) {
            return true;
        }

        return $token->getUser->hasRole($this->actionMapping[$attribute]);
    }
}
