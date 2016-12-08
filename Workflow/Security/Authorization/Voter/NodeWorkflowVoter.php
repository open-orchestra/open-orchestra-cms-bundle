<?php

namespace OpenOrchestra\Workflow\Security\Authorization\Voter;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;

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
     * Check if $subject is in $user perimeter
     *
     * @param StatusableInterface $subject
     * @param UserInterface       $user
     */
    protected function isInPerimeter($subject, UserInterface $user)
    {
        return $this->isSubjectInPerimeter($subject->getPath(), $user, NodeInterface::ENTITY_TYPE);
    }
}
