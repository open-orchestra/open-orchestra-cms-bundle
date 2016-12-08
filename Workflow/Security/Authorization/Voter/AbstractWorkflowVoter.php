<?php

namespace OpenOrchestra\Workflow\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\Authorization\Voter\AbstractPerimeterVoter;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Perimeter\PerimeterManager;
use OpenOrchestra\ModelInterface\Repository\WorkflowProfileRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;

/**
 * Class AbstractWorkflowVoter
 *
 * Voter checking rights on transitions
 */
abstract class AbstractWorkflowVoter extends AbstractPerimeterVoter
{
    protected $workflowRepository;
    
    /**
     * @param PerimeterManager                   $perimeterManager
     * @param WorkflowProfileRepositoryInterface $workflowRepository
     */
    public function __construct(
        PerimeterManager $perimeterManager,
        WorkflowProfileRepositoryInterface $workflowRepository
    ) {
        parent::__construct($perimeterManager);
        $this->workflowRepository = $workflowRepository;
    }

    /**
     * @return array
     */
    protected function getSupportedAttributes()
    {
        return array('OpenOrchestra\ModelInterface\Model\StatusInterface');
    }

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        $isSupportedAttribute = false;

        foreach ($this->getSupportedAttributes() as $supportedAttributeClass) {
            if ($attribute instanceof $supportedAttributeClass) {
                $isSupportedAttribute = true;
                break;
            }
        }

        if (!$isSupportedAttribute) {
            return false;
        }

        foreach ($this->getSupportedClasses() as $supportedClass) {
            if ($subject instanceof StatusableInterface && $subject instanceof $supportedClass) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if $user can update $subject to $status
     *
     * @param UserInterface       $user
     * @param StatusInterface     $status
     * @param StatusableInterface $subject
     *
     * @return boolean
     */
    protected function userCanUpdateToStatus(UserInterface $user, StatusInterface $status, $subject)
    {
        foreach ($user->getGroups() as $group) {
            if ($group->getWorkflowProfileCollection($subject::ENTITY_TYPE)) {
                foreach ($group->getWorkflowProfileCollection($subject::ENTITY_TYPE)->getProfiles() as $profile) {
                    if ($profile->hasTransition($subject->getStatus(), $status)) {

                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * SuperAdmin can use pass a subject from $fromStatus to $toStatus if such a transition exists
     *
     * @param StatusInterface $fromStatus
     * @param StatusInterface StatusInterface $toStatus
     *
     * @return boolean
     */
    protected function voteForSuperAdmin(StatusInterface $fromStatus, StatusInterface $toStatus) {
        if ($this->workflowRepository->hasTransition($fromStatus, $toStatus)) {
            return true;
        }

        return false;
    }
}
