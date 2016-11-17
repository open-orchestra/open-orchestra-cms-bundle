<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Model\PerimeterInterface;

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
