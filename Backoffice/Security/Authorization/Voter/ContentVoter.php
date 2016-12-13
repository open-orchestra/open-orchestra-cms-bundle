<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ContentVoter
 *
 * Voter checking rights on content management
 */
class ContentVoter extends AbstractEditorialVoter
{
    /**
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supportSubject($subject)
    {
        if (is_object($subject)) {
            return $this->supportClasses(
                $subject,
                array('OpenOrchestra\ModelInterface\Model\ContentInterface')
            );
        }

        return $subject === ContentInterface::ENTITY_TYPE;
    }

    /**
     * Vote for Read action
     * A user can read a content if it is in his perimeter
     *
     * @param ContentInterface|string $subject
     * @param TokenInterface          $token
     *
     * @return bool
     */
    protected function voteForReadAction($subject, TokenInterface $token)
    {
        $user = $token->getUser();
        $contentType = $subject;
        if (is_object($subject)) {
            $contentType = $subject->getContentType();
        }

        return $this->isSubjectInPerimeter($contentType, $user, ContentInterface::ENTITY_TYPE);
    }

    /**
     * Vote for $action on $content owned by $user
     * A user can act on his own contents if he has the CONTENT_CONTRIBUTOR role and the content is in his perimeter 
     *
     * @param string           $action
     * @param ContentInterface $content
     * @param TokenInterface   $token
     *
     * @return bool
     */
    protected function voteForOwnedSubject($action, $content, TokenInterface $token)
    {
        $user = $token->getUser();

        return $this->hasRole($token, ContributionRoleInterface::CONTENT_CONTRIBUTOR)
            && $this->isSubjectInPerimeter($content->getContentType(), $user, ContentInterface::ENTITY_TYPE);
    }

    /**
     * Vote for $action on $content not owned by $user
     * A user can act on someone else's content if he has the matching super role and the content is in his perimeter
     *
     * @param string                  $action
     * @param ContentInterface|string $subject
     * @param TokenInterface          $token
     *
     * @return bool
     */
    protected function voteForSomeoneElseSubject($action, $subject, TokenInterface $token)
    {
        if (!is_object($subject)) {
            return false;
        }

        $user = $token->getUser();
        $requiredRole = ContributionRoleInterface::CONTENT_CONTRIBUTOR;

        switch ($action) {
            case ContributionActionInterface::EDIT:
                $requiredRole = ContributionRoleInterface::CONTENT_SUPER_EDITOR;
            break;
            case ContributionActionInterface::DELETE:
                $requiredRole = ContributionRoleInterface::CONTENT_SUPER_SUPRESSOR;
            break;
        }

        return $this->hasRole($token, $requiredRole)
            && $this->isSubjectInPerimeter($subject->getContentType(), $user, ContentInterface::ENTITY_TYPE);
    }
}
