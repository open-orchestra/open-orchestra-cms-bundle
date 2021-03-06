<?php

namespace OpenOrchestra\Workflow\Security\Authorization\Voter;

use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;

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
     * Check if $subject is in $user perimeter
     *
     * @param StatusableInterface $subject
     * @param UserInterface       $user
     *
     * @return boolean
     */
    protected function isInPerimeter($subject, UserInterface $user)
    {
        return $this->isSubjectInPerimeter($subject->getContentType(), $user, ContentTypeInterface::ENTITY_TYPE);
    }

    /**
     * @param StatusableInterface $subject
     *
     * @return string
     */
    protected function getSubjectEntityType($subject)
    {
        return $subject->getContentType();
    }
}
