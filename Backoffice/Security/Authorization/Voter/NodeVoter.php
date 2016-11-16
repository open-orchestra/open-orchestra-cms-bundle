<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;

/**
 * Class NodeVoter
 *
 * Voter checking rights on node management
 */
class NodeVoter extends AbstractPerimeterVoter
{
    /**
     * @return array
     */
    protected function getSupportedClasses()
    {
        return array('OpenOrchestra\ModelInterface\Model\NodeInterface');
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
            return true;
        }

        if ($subject->getCreatedBy() == $user->getUsername()) {
            return $this->voteForOwnedNode($attribute, $subject, $user);
        }

        return $this->voteForSomeoneElseNode($attribute, $subject, $user);
    }

    /**
     * Vote for $action on $node owned by $user
     *
     * @param string        $action
     * @param NodeInterface $node
     * @param UserInterface $user
     *
     * @return bool
     */
    protected function voteForOwnedNode($action, NodeInterface $node, UserInterface $user)
    {
        return $user->hasRole(ContributionRoleInterface::NODE_CONTRIBUTOR)
            && $this->isSubjectInAllowedPerimeter($node->getPath(), $user, NodeInterface::ENTITY_TYPE);
    }

    /**
     * Vote for $action on $node not owned by $user
     *
     * @param string        $action
     * @param NodeInterface $node
     * @param UserInterface $user
     *
     * @return bool
     */
    protected function voteForSomeoneElseNode($action, NodeInterface $node, UserInterface $user)
    {
        $requiredRole = ContributionRoleInterface::NODE_CONTRIBUTOR;

        switch ($action) {
            case ContributionActionInterface::EDIT:
                $requiredRole = ContributionRoleInterface::NODE_SUPER_EDITOR;
            break;
            case ContributionActionInterface::DELETE:
                $requiredRole = ContributionRoleInterface::NODE_SUPER_SUPRESSOR;
            break;
        }

        return $user->hasRole($requiredRole)
            && $this->isSubjectInAllowedPerimeter($node->getPath(), $user, NodeInterface::ENTITY_TYPE);
    }
}
