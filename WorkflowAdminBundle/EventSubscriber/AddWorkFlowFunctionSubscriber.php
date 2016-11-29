<?php

namespace OpenOrchestra\WorkflowAdminBundle\EventSubscriber;

use OpenOrchestra\UserBundle\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\Workflow\WorkflowRightEvents;
use OpenOrchestra\Workflow\Event\WorkflowRightEvent;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Class AddWorkFlowFunctionSubscriber
 */
class AddWorkFlowFunctionSubscriber implements EventSubscriberInterface
{
    protected $userRepository;

    /**
     * @param UserRepository  $userRepository
     * @param DocumentManager $documentManager
     */
    public function __construct(UserRepository $userRepository, DocumentManager $documentManager)
    {
        $this->userRepository = $userRepository;
        $this->documentManager = $documentManager;
    }

    /**
     * @param WorkflowRightEvent $event
     */
    public function postUserUpdate(WorkflowRightEvent $event)
    {
        $workfolwRight = $event->getWorkflowRight();
        $user = $this->userRepository->find($workfolwRight->getUserId());

        $newRoles = array();

        $authorizations = $workfolwRight->getAuthorizations();
        foreach ($authorizations as $authorization) {
            $workflowFunctions = $authorization->getWorkflowFunctions();
            foreach ($workflowFunctions as $workflowFunction) {
                $roles = $workflowFunction->getRoles();
                foreach ($roles as $role) {
                    $newRoles[] = $role->getName();
                }
            }
        }
        $newRoles = array_diff(array_unique($newRoles), $user->getRoles());
        foreach ($newRoles as $newRole) {
            $user->addRole($newRole);
        }
        $this->documentManager->flush($user);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            WorkflowRightEvents::WORKFLOWRIGHT_UPDATE => 'postUserUpdate',
        );
    }
}
