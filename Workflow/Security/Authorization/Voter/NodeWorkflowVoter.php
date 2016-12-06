<?php

namespace OpenOrchestra\Workflow\Security\Authorization\Voter;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class NodeWorkflowVoter
 *
 * Voter checking rights on node transitions
 */
class NodeWorkflowVoter extends AbstractWorkflowVoter
{
    /**
     * @return array
     */
    protected function getSupportedClasses()
    {
        return array('OpenOrchestra\ModelInterface\Model\NodeInterface');
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
            if ($this->transitionExists($attribute)) {
                return VoterInterface::ACCESS_GRANTED;
            }

            return VoterInterface::ACCESS_DENIED;
        }

        if (!$this->isSubjectInPerimeter($subject->getPath(), $user, NodeInterface::ENTITY_TYPE)) {
            return VoterInterface::ACCESS_DENIED;
        }

        if ($this->userHasTransitionOnEntity($user, $attribute, NodeInterface::ENTITY_TYPE)) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_DENIED;
    }

    protected function voteForSuperAdmin() {
        /**
         * @todo Check if $transition exists in the profile repository
         * TransitionRepository->hasTransition()
         **/
        return VoterInterface::ACCESS_GRANTED;
    }
}
