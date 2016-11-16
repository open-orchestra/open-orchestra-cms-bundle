<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\Backoffice\Model\PerimeterInterface;

/**
 * Class AbstractVoter
 *
 * Abstract class for voters associated with a perimeter
 */
abstract class AbstractPerimeterVoter extends Voter
{
    /**
     * If you have a simple voter triggering on certain classes and certain attributes,
     * only override the getSupportedClasses and getSupportedAttributes methods.
     *
     * If you have a more complex supports mixing both attribute and subject,
     * then overide this method
     *
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
     * If you have a simple voter triggering on certain classes and certain attributes,
     * you can override this method to return the list of supported classes
     *
     * @return array
     */
    protected function getSupportedClasses()
    {
        return array();
    }

    /**
     * If you have a simple voter triggering on certain classes and certain attributes,
     * you can ovveride this method to return the list of supported attributes
     *
     * @return array
     */
    protected function getSupportedAttributes()
    {
        return array();
    }

    /**
     * @param UserInterface|string $user
     *
     * @return bool
     */
    protected function isSuperAdmin($user = null)
    {
        return ($user instanceof UserInterface
            && ($user->hasRole(ContributionRoleInterface::DEVELOPER) || $user->hasRole(ContributionRoleInterface::PLATFORM_ADMIN))
        );
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
