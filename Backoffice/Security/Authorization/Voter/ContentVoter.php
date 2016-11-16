<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

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
     * Vote for Read action
     *
     * @param ContentInterface $content
     * @param UserInterface    $user
     *
     * @return bool
     */
    protected function voteForReadAction($content, $user)
    {
        return $this->isSubjectInAllowedPerimeter($subject->getContentType(), $user, ContentInterface::ENTITY_TYPE);
    }

    /**
     * Vote for $action on $content owned by $user
     *
     * @param string           $action
     * @param ContentInterface $content
     * @param UserInterface    $user
     *
     * @return bool
     */
    protected function voteForOwnedSubject($action, $content, UserInterface $user)
    {
        return $user->hasRole(ContributionRoleInterface::CONTENT_CONTRIBUTOR)
            && $this->isSubjectInAllowedPerimeter($content->getContentType(), $user, ContentInterface::ENTITY_TYPE);
    }

    /**
     * Vote for $action on $content not owned by $user
     *
     * @param string           $action
     * @param ContentInterface $content
     * @param UserInterface    $user
     *
     * @return bool
     */
    protected function voteForSomeoneElseSubject($action, $content, UserInterface $user)
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
            && $this->isSubjectInAllowedPerimeter($content->getContentType(), $user, ContentInterface::ENTITY_TYPE);
    }
}
