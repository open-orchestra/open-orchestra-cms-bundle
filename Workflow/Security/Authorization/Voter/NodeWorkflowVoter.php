<?php

namespace OpenOrchestra\Workflow\Security\Authorization\Voter;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
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
     * @return boolean
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if ($this->isSuperAdmin($user)) {
            return $this->voteForSuperAdmin($subject->getStatus(), $attribute);
        }

        if (!$this->isSubjectInPerimeter($subject->getPath(), $user, NodeInterface::ENTITY_TYPE)) {

            return false;
        }

        return ($this->userCanUpdateToStatus($user, $attribute, $subject));
    }
}
