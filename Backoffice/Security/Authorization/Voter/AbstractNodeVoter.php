<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class NodeVoter
 *
 * Abstract Voter checking rights on node management
 */
abstract class AbstractNodeVoter extends AbstractEditorialVoter
{
    /**
     * @param mixed $node
     *
     * @return string
     */
    abstract protected function getPath($node);

    /**
     * Vote for Read action
     * A user can read a node if it is in his perimeter
     *
     * @param NodeInterface  $node
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteForReadAction($node, TokenInterface $token)
    {
        return $this->hasRole($token, ContributionRoleInterface::NODE_CONTRIBUTOR)
            && $this->isSubjectInPerimeter($this->getPath($node), $token->getUser(), NodeInterface::ENTITY_TYPE);
    }

    /**
     * Vote for $action on $node owned by $user
     * A user can act on his own nodes if he has the NODE_CONTRIBUTOR role and the node is in his perimeter
     *
     * @param string         $action
     * @param NodeInterface  $node
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteForOwnedSubject($action, $node, TokenInterface $token)
    {
        return $this->hasRole($token, ContributionRoleInterface::NODE_CONTRIBUTOR)
            && $this->isSubjectInPerimeter($this->getPath($node), $token->getUser(), NodeInterface::ENTITY_TYPE);
    }

    /**
     * Vote for $action on $node not owned by $user
     * A user can act on someone else's node if he has the matching super role and the node is in his perimeter
     *
     * @param string         $action
     * @param NodeInterface  $node
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteForSomeoneElseSubject($action, $node, TokenInterface $token)
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

        return $this->hasRole($token, $requiredRole)
            && $this->isSubjectInPerimeter($this->getPath($node), $token->getUser(), NodeInterface::ENTITY_TYPE);
    }
}
