<?php

namespace OpenOrchestra\Workflow\Security\Authorization\Voter;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use OpenOrchestra\ModelInterface\Repository\WorkflowProfileRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\WorkflowTransitionInterface;
use OpenOrchestra\Backoffice\Perimeter\PerimeterManager;

/**
 * Class NodeWorkflowVoter
 *
 * Voter checking rights on node transitions
 */
class NodeWorkflowVoter extends AbstractWorkflowVoter
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
    protected function getSupportedClasses()
    {
        return array('OpenOrchestra\ModelInterface\Model\NodeInterface');
    }

    /**
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return boolean
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if ($this->isSuperAdmin($user)) {
            if ($this->transitionExists($attribute)) {
                return $this->voteForSuperAdmin($attribute);
            }

            return false;
        }

        if (!$this->isSubjectInPerimeter($subject->getPath(), $user, NodeInterface::ENTITY_TYPE)) {
            return false;
        }

        if ($this->userHasTransitionOnEntity($user, $attribute, NodeInterface::ENTITY_TYPE)) {
            return true;
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
