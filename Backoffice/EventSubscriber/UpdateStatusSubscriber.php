<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\Backoffice\Exception\StatusChangeNotGrantedException;
use OpenOrchestra\ModelInterface\Event\StatusableEvent;
use OpenOrchestra\ModelInterface\StatusEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class UpdateStatusSubscriber
 */
class UpdateStatusSubscriber implements EventSubscriberInterface
{
    protected $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param StatusableEvent $event
     *
     * @throws StatusChangeNotGrantedException
     */
    public function updateStatus(StatusableEvent $event)
    {
        $document = $event->getStatusableElement();
        $toStatus = $event->getToStatus();
        if ($this->authorizationChecker->isGranted($toStatus, $document)) {
            $document->setStatus($toStatus);
        } else {
            throw new StatusChangeNotGrantedException();
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
