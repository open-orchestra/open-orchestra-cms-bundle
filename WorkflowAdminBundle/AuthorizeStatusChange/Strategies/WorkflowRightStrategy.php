<?php

namespace OpenOrchestra\WorkflowAdminBundle\AuthorizeStatusChange\Strategies;

use OpenOrchestra\Backoffice\AuthorizeStatusChange\AuthorizeStatusChangeInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;
use OpenOrchestra\Workflow\Repository\WorkflowFunctionRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class WorkflowRightStrategy
 */
class WorkflowRightStrategy implements AuthorizeStatusChangeInterface
{
    protected $authorizationChecker;
    protected $roleRepository;
    protected $workflowFunctionRepository;

    /**
     * @param AuthorizationCheckerInterface       $authorizationChecker
     * @param RoleRepositoryInterface             $roleRepository
     * @param WorkflowFunctionRepositoryInterface $workflowFunctionRepository
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        RoleRepositoryInterface $roleRepository,
        WorkflowFunctionRepositoryInterface $workflowFunctionRepository
    ){
        $this->authorizationChecker = $authorizationChecker;
        $this->roleRepository = $roleRepository;
        $this->workflowFunctionRepository = $workflowFunctionRepository;
    }

    /**
     * @param StatusableInterface $document
     * @param StatusInterface     $toStatus
     *
     * @return bool
     */
    public function isGranted(StatusableInterface $document, StatusInterface $toStatus)
    {
        $fromStatus = $document->getStatus();
        if ($fromStatus->getId() != $toStatus->getId()) {
            $role = $this->roleRepository->findOneByFromStatusAndToStatus($fromStatus, $toStatus);
            $workflowFunctions = $this->workflowFunctionRepository->findByRole($role);
            foreach ($workflowFunctions as $workflowFunction) {
                if ($this->authorizationChecker->isGranted($workflowFunction->getId(), $document)) {

                    return true;
                }
            }

            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'workflow_right';
    }
}
