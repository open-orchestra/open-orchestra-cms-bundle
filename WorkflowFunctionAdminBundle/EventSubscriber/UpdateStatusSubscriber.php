<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\StatusableEvent;
use OpenOrchestra\ModelInterface\StatusEvents;
use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;
use OpenOrchestra\WorkflowFunction\Repository\WorkflowFunctionRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class UpdateStatusSubscriber
 */
class UpdateStatusSubscriber implements EventSubscriberInterface
{
    protected $authorizationChecker;
    protected $roleRepository;

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
     */
    public function updateStatus(StatusableEvent $event)
    {
        $document = $event->getStatusableElement();
        $fromStatus = $event->getFromStatus();
        $toStatus = $document->getStatus();
        if ($fromStatus->getId() != $toStatus->getId()) {
            $role = $this->roleRepository->findOneByFromStatusAndToStatus($fromStatus, $toStatus);
            $workflowFunctions = $this->workflowFunctionRepository->findByRole($role);
            $attributes = array();
            foreach($workflowFunctions as $workflowFunction){
                $attributes[] = $workflowFunction->getId();
            }
            if (!$this->authorizationChecker->isGranted($attributes, $document)) {
                $document->setStatus($fromStatus);
            }
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            StatusEvents::STATUS_CHANGE => 'updateStatus',
        );
    }
}
