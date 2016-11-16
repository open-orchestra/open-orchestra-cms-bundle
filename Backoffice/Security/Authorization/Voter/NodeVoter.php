<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

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
     * Vote for Read action
     *
     * @param NodeInterface $node
     * @param UserInterface $user
     *
     * @return bool
     */
    protected function voteForReadAction($node, $user)
    {
        return true;
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
    protected function voteForOwnedSubject($action, $node, UserInterface $user)
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
    protected function voteForSomeoneElseSubject($action, $node, UserInterface $user)
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
