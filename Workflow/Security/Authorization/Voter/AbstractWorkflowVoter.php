<?php

namespace OpenOrchestra\Workflow\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Security\Authorization\Voter\AbstractPerimeterVoter;
use OpenOrchestra\ModelInterface\Model\WorkflowTransitionInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Perimeter\PerimeterManager;
use OpenOrchestra\ModelInterface\Repository\WorkflowProfileRepositoryInterface;

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
        ){
            parent::__construct($perimeterManager);
            $this->workflowRepository = $workflowRepository;
    }

    /**
     * @return array
     */
    protected function getSupportedAttributes()
    {
        return array('OpenOrchestra\ModelInterface\Model\WorkflowTransitionInterface');
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
            if ($subject instanceof $supportedClass) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if $user can use $transition on $entityType
     *
     * @param UserInterface               $user
     * @param WorkflowTransitionInterface $transition
     * @param string                      $entityType
     *
     * @return boolean
     */
    protected function userHasTransitionOnEntity(UserInterface $user, WorkflowTransitionInterface $transition, $entityType)
    {
        foreach ($user->getGroups() as $group) {
            if ($group->getWorkflowProfileCollection($entityType)) {
                foreach ($group->getWorkflowProfileCollection($entityType) as $profile) {
                    if ($profile->hasTransition($transition)) {

                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * SuperAdmin can use $transition if $transition exists
     *
     * @param WorkflowTransitionInterface $transition
     *
     * @return boolean
     */
    protected function voteForSuperAdmin(WorkflowTransitionInterface $transition) {
        if ($this->workflowRepository->hasTransition($transition)) {
            return true;
        }

        return false;
    }
}
