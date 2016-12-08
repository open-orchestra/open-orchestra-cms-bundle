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
    protected $cachedPerimeters = array();

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
     * @param string        $subjectKey
     * @param UserInterface $user
     * @param string        $entityType
     *
     * @return bool
     */
    protected function isSubjectInPerimeter($subjectKey, UserInterface $user, $entityType)
    {
        if (false === $this->hasCachedPerimeters($user, $entityType)) {
            $cachedPerimeter = $this->perimeterManager->createPerimeter($entityType);
            foreach ($user->getGroups() as $group) {
                $perimeter = $group->getPerimeter($entityType);
                if ($perimeter instanceof PerimeterInterface) {
                    $cachedPerimeter->addItems($perimeter->getItems());
                }
            }

            $this->cachedPerimeters[$user->getId()][$entityType] = $cachedPerimeter;
        }

        return $this->perimeterManager->isInPerimeter($subjectKey, $this->cachedPerimeters[$user->getId()][$entityType]);
    }

    /**
     * @param UserInterface $user
     * @param string        $entityType
     *
     * @return bool
     */
    protected function hasCachedPerimeters(UserInterface $user, $entityType)
    {
        $userId = $user->getId();

        return array_key_exists($userId, $this->cachedPerimeters) &&
               array_key_exists($entityType, $this->cachedPerimeters[$userId]) &&
               $this->cachedPerimeters[$userId] instanceof PerimeterInterface;
    }
}
