<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Model\PerimeterInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Perimeter\PerimeterManager;

/**
 * Class AbstractPerimeterVoter
 *
 * Abstract class for voters associated with a perimeter
 */
abstract class AbstractPerimeterVoter extends AbstractVoter
{
    protected $perimeterManager;

    /**
     * @param PerimeterManager $perimeterManager
     */
    public function __construct(PerimeterManager $perimeterManager)
    {
        $this->perimeterManager = $perimeterManager;
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
    protected function isSubjectInPerimeter($subjectKey, UserInterface $user, $entityType)
    {
        foreach ($user->getGroups() as $group) {
            $perimeter = $group->getPerimeter($entityType);

            if ($perimeter instanceof PerimeterInterface
                && $this->perimeterManager->isInPerimeter($subjectKey, $perimeter)
            ) {
                return true;
            }
        }

        return false;
    }
}
