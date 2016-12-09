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
     * @param mixed         $subject
     * @param UserInterface $user
     *
     * @return bool
     */
    abstract protected function voteForReadAction($subject, UserInterface $user);

    /**
     * Vote for $action on $subject owned by $user
     *
     * @param string        $action
     * @param mixed         $subject
     * @param UserInterface $user
     *
     * @return bool
     */
    abstract protected function voteForOwnedSubject($action, $subject, UserInterface $user);

    /**
     * Vote for $action on $subject not owned by $user
     *
     * @param string        $action
     * @param mixed         $subject
     * @param UserInterface $user
     *
     * @return bool
     */
    abstract protected function voteForSomeoneElseSubject($action, $subject, UserInterface $user);

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
            return true;
        }

        if (ContributionActionInterface::CREATE === $attribute ||
            ContributionActionInterface::READ === $attribute
        )  {
            return $this->voteForReadAction($subject, $user);
        }

        if ($this->isCreator($subject, $user)) {
            return $this->voteForOwnedSubject($attribute, $subject, $user);
        }

        return $this->voteForSomeoneElseSubject($attribute, $subject, $user);
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
