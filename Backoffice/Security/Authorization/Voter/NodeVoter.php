<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Model\PerimeterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;

/**
 * Class NodeVoter
 */
class NodeVoter extends AbstractVoter
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
        return $user->hasRole(ContributionRoleInterface::NODE_SELF) && $this->isNodeInAllowedPerimeter($node, $user);
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
        $requiredRole = ContributionRoleInterface::NODE_SELF;

        switch ($action) {
            case ContributionActionInterface::EDIT:
                $requiredRole = ContributionRoleInterface::NODE_EDIT;
            break;
            case ContributionActionInterface::DELETE:
                $requiredRole = ContributionRoleInterface::NODE_DELETE;
            break;
        }

        return $user->hasRole($requiredRole) && $this->isNodeInAllowedPerimeter($node, $user);
    }

    /**
     * Check if $node is in an allowed perimeter to $user
     *
     * @param NodeInterface $node
     * @param UserInterface $user
     *
     * @return bool
     */
    protected function isNodeInAllowedPerimeter(NodeInterface $node, UserInterface $user)
    {
        foreach ($user->getGroups() as $group) {
            $nodePerimeter = $group->getPerimeter(NodeInterface::ENTITY_TYPE);

            if ($nodePerimeter instanceof PerimeterInterface && $nodePerimeter->contains($node->getPath())) {
                return true;
            }
        }

        return false;
    }
}
