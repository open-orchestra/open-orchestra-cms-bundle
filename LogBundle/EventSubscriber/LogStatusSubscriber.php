<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\StatusEvent;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\StatusEvents;

/**
 * Class LogStatusSubscriber
 */
class LogStatusSubscriber extends AbstractLogSubscriber
{
    /**
     * @param StatusEvent $event
     */
    public function statusCreate(StatusEvent $event)
    {
        $this->sendLog('open_orchestra_log.status.create', $event->getStatus());
    }

    /**
     * @param StatusEvent $event
     */
    public function statusDelete(StatusEvent $event)
    {
        $this->sendLog('open_orchestra_log.status.delete', $event->getStatus());
    }

    /**
     * @param StatusEvent $event
     */
    public function statusUpdate(StatusEvent $event)
    {
        $this->sendLog('open_orchestra_log.status.update', $event->getStatus());
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            StatusEvents::STATUS_CREATE => 'statusCreate',
            StatusEvents::STATUS_DELETE => 'statusDelete',
            StatusEvents::STATUS_UPDATE => 'statusUpdate',
        );
    }

    /**
     * @param string          $message
     * @param StatusInterface $status
     */
    protected function sendLog($message, StatusInterface $status)
    {
        $this->logger->info($message, array('status_name' => $status->getName()));
    }
}
