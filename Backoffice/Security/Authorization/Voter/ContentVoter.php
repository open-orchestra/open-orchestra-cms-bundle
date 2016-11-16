<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;

/**
 * Class ContentVoter
 *
 * Voter checking rights on content management
 */
class ContentVoter extends AbstractPerimeterVoter
{
    /**
     * @return array
     */
    protected function getSupportedClasses()
    {
        return array('OpenOrchestra\ModelInterface\Model\ContentInterface');
    }

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

        if (ContributionActionInterface::READ == $attribute) {
            return $this->isSubjectInAllowedPerimeter($subject, $user, ContentInterface::ENTITY_TYPE);
        }

        if ($subject->getCreatedBy() == $user->getUsername()) {
            return $this->voteForOwnedContent($attribute, $subject, $user);
        }

        return $this->voteForSomeoneElseContent($attribute, $subject, $user);
    }

    /**
     * Vote for $action on $content owned by $user
     *
     * @param string           $action
     * @param ContentInterface $node
     * @param UserInterface    $user
     *
     * @return bool
     */
    protected function voteForOwnedContent($action, ContentInterface $content, UserInterface $user)
    {
        return $user->hasRole(ContributionRoleInterface::CONTENT_CONTRIBUTOR)
            && $this->isSubjectInAllowedPerimeter($content, $user, ContentInterface::ENTITY_TYPE);
    }

    /**
     * Vote for $action on $content not owned by $user
     *
     * @param string           $action
     * @param ContentInterface $node
     * @param UserInterface    $user
     *
     * @return bool
     */
    protected function voteForSomeoneElseNode($action, ContentInterface $content, UserInterface $user)
    {
        $requiredRole = ContributionRoleInterface::CONTENT_CONTRIBUTOR;

        switch ($action) {
            case ContributionActionInterface::EDIT:
                $requiredRole = ContributionRoleInterface::CONTENT_SUPER_EDITOR;
            break;
            case ContributionActionInterface::DELETE:
                $requiredRole = ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR;
            break;
        }

        return $user->hasRole($requiredRole)
            && $this->isSubjectInAllowedPerimeter($content, $user, ContentInterface::ENTITY_TYPE);
    }
}
