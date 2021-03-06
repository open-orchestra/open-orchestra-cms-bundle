<?php

namespace OpenOrchestra\Workflow\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\Backoffice\Security\Authorization\Voter\AbstractPerimeterVoter;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Perimeter\PerimeterManager;
use OpenOrchestra\ModelInterface\Repository\WorkflowProfileRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

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
     * @param AccessDecisionManagerInterface     $decisionManager
     */
    public function __construct(
        AccessDecisionManagerInterface $decisionManager,
        PerimeterManager $perimeterManager,
        WorkflowProfileRepositoryInterface $workflowRepository
    ) {
        parent::__construct($decisionManager,$perimeterManager);
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

        return $isSupportedAttribute && $this->supportSubject($subject);
    }

    /**
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supportSubject($subject)
    {
        foreach ($this->getSupportedClasses() as $supportedClass) {
            if ($subject instanceof StatusableInterface && $subject instanceof $supportedClass) {
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
        if ($this->isSuperAdmin($token)) {
            return $this->voteForSuperAdmin($subject->getStatus(), $attribute);
        }

        $user = $token->getUser();
        if (!$this->isInPerimeter($subject, $user)) {
            return false;
        }

        return $this->userCanUpdateToStatus($user, $attribute, $subject);
    }

    /**
     * Check if $subject is in $user perimeter
     *
     * @param StatusableInterface $subject
     * @param UserInterface       $user
     */
    abstract protected function isInPerimeter($subject, UserInterface $user);

    /**
     * SuperAdmin can use pass a subject from $fromStatus to $toStatus if such a transition exists
     *
     * @param StatusInterface $fromStatus
     * @param StatusInterface StatusInterface $toStatus
     *
     * @return boolean
     */
    protected function voteForSuperAdmin(StatusInterface $fromStatus, StatusInterface $toStatus) {
        return $this->workflowRepository->hasTransition($fromStatus, $toStatus);
    }

    /**
     * @param StatusableInterface $subject
     *
     * @return string
     */
    protected function getSubjectEntityType($subject)
    {
        return $subject::ENTITY_TYPE;
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
        /** @var GroupInterface $group */
        foreach ($user->getGroups() as $group) {
            $entityType = $this->getSubjectEntityType($subject);
            if (!$group->isDeleted() && $group->getWorkflowProfileCollection($entityType)) {
                foreach ($group->getWorkflowProfileCollection($entityType)->getProfiles() as $profile) {
                    if ($profile->hasTransition($subject->getStatus(), $status)) {

                        return true;
                    }
                }
            }
        }

        return false;
    }
}
