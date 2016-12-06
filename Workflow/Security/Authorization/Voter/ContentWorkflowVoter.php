<?php

namespace OpenOrchestra\Workflow\Security\Authorization\Voter;

use OpenOrchestra\ModelInterface\Model\ContentInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ContentWorkflowVoter
 *
 * Voter checking rights on content transitions
 */
class ContentWorkflowVoter extends AbstractWorkflowVoter
{
    /**
     * @return array
     */
    protected function getSupportedClasses()
    {
        return array('OpenOrchestra\ModelInterface\Model\ContentInterface');
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
            return $this->voteForSuperAdmin($attribute);
        }

        if (!$this->isSubjectInPerimeter($subject->getContentType(), $user, ContentInterface::ENTITY_TYPE)) {
            return false;
        }

        if ($this->userHasTransitionOnEntity($user, $attribute, ContentInterface::ENTITY_TYPE)) {
            return true;
        }

        return false;
    }
}
