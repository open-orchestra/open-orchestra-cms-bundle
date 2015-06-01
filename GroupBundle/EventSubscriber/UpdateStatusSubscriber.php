<?php

namespace OpenOrchestra\GroupBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\StatusableEvent;
use OpenOrchestra\ModelInterface\StatusEvents;
use OpenOrchestra\ModelInterface\Repository\RoleRepositoryInterface;
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
     */
    public function updateStatus(StatusableEvent $event)
    {
        $document = $event->getStatusableElement();
        $fromStatus = $event->getFromStatus();
        $toStatus = $document->getStatus();
        $role = $this->roleRepository->findOneByFromStatusAndToStatus($fromStatus, $toStatus);
        if ($role && !$this->authorizationChecker->isGranted(array($role->getName()))) {
            $document->setStatus($fromStatus);
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
