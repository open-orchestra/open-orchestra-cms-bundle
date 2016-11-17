<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Model\PerimeterInterface;

/**
 * Class AbstractPerimeterVoter
 *
 * Abstract class for voters associated with a perimeter
 */
abstract class AbstractPerimeterVoter extends AbstractVoter
{
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
