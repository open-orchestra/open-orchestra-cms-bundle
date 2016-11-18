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
class ContentVoter extends AbstractEditorialVoter
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
     * A user can read a content if it is in his perimeter
     *
     * @param ContentInterface $content
     * @param UserInterface    $user
     *
     * @return bool
     */
    protected function voteForReadAction($content, $user)
    {
        return $this->isSubjectInAllowedPerimeter($content->getContentType(), $user, ContentInterface::ENTITY_TYPE);
    }

    /**
     * Vote for $action on $content owned by $user
     * A user can act on his own contents if he has the CONTENT_CONTRIBUTOR role and the content is in his perimeter 
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
     * A user can act on someone else's content if he has the matching super role and the content is in his perimeter
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
