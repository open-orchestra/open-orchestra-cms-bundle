<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\ModelInterface\Model\BlameableInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class AbstractEditorialVoter
 *
 * Abstract class for voters associated with editorial $subject
 */
abstract class AbstractEditorialVoter extends AbstractPerimeterVoter
{
    /**
     * Vote for Read action
     *
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    abstract protected function voteForReadAction($subject, TokenInterface $token);

    /**
     * Vote for $action on $subject owned by $user
     *
     * @param string         $action
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    abstract protected function voteForOwnedSubject($action, $subject, TokenInterface $token);

    /**
     * Vote for $action on $subject not owned by $user
     *
     * @param string         $action
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    abstract protected function voteForSomeoneElseSubject($action, $subject, TokenInterface $token);

    /**
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($this->isSuperAdmin($token)) {
            return true;
        }

        if (ContributionActionInterface::READ === $attribute) {
            return $this->voteForReadAction($subject, $token);
        }

        if ($this->isCreator($subject, $token->getUser())) {
            return $this->voteForOwnedSubject($attribute, $subject, $token);
        }

        return $this->voteForSomeoneElseSubject($attribute, $subject, $token);
    }

    /**
     * @param mixed         $subject
     * @param UserInterface $user
     *
     * @return string
     */
    protected function isCreator($subject, UserInterface $user)
    {
        return $subject instanceof BlameableInterface &&
               $subject->getCreatedBy() === $user->getUsername();
    }
}
