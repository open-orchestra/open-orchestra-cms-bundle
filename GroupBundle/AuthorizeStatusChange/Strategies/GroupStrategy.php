<?php

namespace OpenOrchestra\GroupBundle\AuthorizeStatusChange\Strategies;

use OpenOrchestra\Backoffice\AuthorizeStatusChange\AuthorizeStatusChangeInterface;

use OpenOrchestra\ModelInterface\Event\StatusableEvent;
use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;
use OpenOrchestra\WorkflowFunction\Repository\WorkflowFunctionRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class GroupStrategy
 */
class GroupStrategy implements AuthorizeStatusChangeInterface
{
    protected $authorizationChecker;
    protected $roleRepository;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param RoleRepositoryInterface       $roleRepository
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, RoleRepositoryInterface $roleRepository)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->roleRepository = $roleRepository;
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
        $role = $this->roleRepository->findOneByFromStatusAndToStatus($fromStatus, $toStatus);

        return !($role && !$this->authorizationChecker->isGranted(array($role->getName())));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'group';
    }
}
