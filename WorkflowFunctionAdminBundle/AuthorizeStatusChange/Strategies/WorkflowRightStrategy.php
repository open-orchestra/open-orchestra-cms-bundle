<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\AuthorizeStatusChange\Strategies;

use OpenOrchestra\Backoffice\AuthorizeStatusChange\AuthorizeStatusChangeInterface;

use OpenOrchestra\ModelInterface\Event\StatusableEvent;
use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;
use OpenOrchestra\WorkflowFunction\Repository\WorkflowFunctionRepositoryInterface;
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
        WorkflowFunctionRepositoryInterface $workflowFunctionRepository)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->roleRepository = $roleRepository;
        $this->workflowFunctionRepository = $workflowFunctionRepository;
    }

    /**
     * @param StatusableEvent $event
     *
     * @return bool
     */
    public function isGranted(StatusableEvent $event)
    {
        $document = $event->getStatusableElement();
        $fromStatus = $document->getStatus();
        $toStatus = $event->getToStatus();
        if ($fromStatus->getId() != $toStatus->getId()) {
            $role = $this->roleRepository->findOneByFromStatusAndToStatus($fromStatus, $toStatus);
            $workflowFunctions = $this->workflowFunctionRepository->findByRole($role);
            $attributes = array();
            foreach ($workflowFunctions as $workflowFunction) {
                $attributes[] = $workflowFunction->getId();
            }
            if (!$this->authorizationChecker->isGranted($attributes, $document)) {
                return false;
            }
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
