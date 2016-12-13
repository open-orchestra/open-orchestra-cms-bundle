<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Model\PerimeterInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Perimeter\PerimeterManager;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

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
     * @param AccessDecisionManagerInterface $decisionManager
     * @param PerimeterManager              $perimeterManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager, PerimeterManager $perimeterManager)
    {
        parent::__construct($decisionManager);
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
        $perimeter = $this->getAgglomeratedPerimeter($user, $entityType);

        return $this->perimeterManager->isInPerimeter($subjectKey, $perimeter);
    }

    /**
     * @param UserInterface $user
     * @param string        $entityType
     *
     * @return PerimeterInterface
     */
    protected function getAgglomeratedPerimeter(UserInterface $user, $entityType)
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

        return $this->cachedPerimeters[$user->getId()][$entityType];
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
