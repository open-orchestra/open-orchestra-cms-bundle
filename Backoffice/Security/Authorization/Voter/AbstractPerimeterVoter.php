<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Model\PerimeterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class AbstractPerimeterVoter
 *
 * Abstract class for voters associated with a perimeter
 */
abstract class AbstractPerimeterVoter extends AbstractVoter
{
    /**
     * Return the list of supported classes
     *
     * @return array
     */
    abstract protected function getSupportedClasses();

    /**
     * @return array
     */
    protected function getSupportedAttributes()
    {
        return array(
            ContributionActionInterface::READ,
            ContributionActionInterface::ADD,
            ContributionActionInterface::EDIT,
            ContributionActionInterface::DELETE
        );
    }

    /**
     * Vote for Read action
     *
     * @param mixed         $subject
     * @param UserInterface $user
     *
     * @return bool
     */
    abstract protected function voteForReadAction($subject, $user);

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
     * @param string $attribute
     * @param mixed  $subject
     *
     * return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, $this->getSupportedAttributes())) {
            return false;
        }

        foreach ($this->getSupportedClasses() as $supportedClass) {
            if ($subject instanceof $supportedClass) {
                return true;
            }
        }

        return false;
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
            return true;
        }

        if (ContributionActionInterface::READ == $attribute) {
            return $this->voteForReadAction($subject, $user);
        }

        if ($subject->getCreatedBy() == $user->getUsername()) {
            return $this->voteForOwnedSubject($attribute, $subject, $user);
        }

        return $this->voteForSomeoneElseSubject($attribute, $subject, $user);
    }

    /**
     * Check if $subjectKey is in an allowed perimeter to $user
     * The perimeter to check is of $entityType
     *
     * @param string        $subject
     * @param UserInterface $user
     * @param string        $entityType
     *
     * @return bool
     */
    protected function isSubjectInAllowedPerimeter($subjectKey, UserInterface $user, $entityType)
    {
        foreach ($user->getGroups() as $group) {
            $perimeter = $group->getPerimeter($entityType);

            if ($perimeter instanceof PerimeterInterface && $perimeter->contains($subjectKey)) {
                return true;
            }
        }

        return false;
    }
}
