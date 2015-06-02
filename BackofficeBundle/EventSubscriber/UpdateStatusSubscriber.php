<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\StatusableEvent;
use OpenOrchestra\ModelInterface\StatusEvents;
use OpenOrchestra\BackofficeBundle\StrategyManager\AuthorizeStatusChangeManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateStatusSubscriber
 */
class UpdateStatusSubscriber implements EventSubscriberInterface
{
    protected $authorizeStatusChangeManager;

    /**
     * @param AuthorizeStatusChangeManager $authorizeStatusChangeManager
     */
    public function __construct(AuthorizeStatusChangeManager $authorizeStatusChangeManager)
    {
        $this->authorizeStatusChangeManager = $authorizeStatusChangeManager;
    }

    /**
     * @param StatusableEvent $event
     */
    public function updateStatus(StatusableEvent $event)
    {
        $document = $event->getStatusableElement();
        $toStatus = $event->getToStatus();
        if ($this->authorizeStatusChangeManager->isGranted($document, $toStatus)) {
            $document->setStatus($toStatus);
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
