<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\StatusEvent;
use OpenOrchestra\ModelInterface\StatusEvents;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogStatusSubscriber
 */
class LogStatusSubscriber implements EventSubscriberInterface
{
    protected $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param StatusEvent $event
     */
    public function statusCreate(StatusEvent $event)
    {
        $status = $event->getStatus();
        $this->logger->info('open_orchestra_log.status.create', array('status_name' => $status->getName()));
    }

    /**
     * @param StatusEvent $event
     */
    public function statusDelete(StatusEvent $event)
    {
        $status = $event->getStatus();
        $this->logger->info('open_orchestra_log.status.delete', array('status_name' => $status->getName()));
    }

    /**
     * @param StatusEvent $event
     */
    public function statusUpdate(StatusEvent $event)
    {
        $status = $event->getStatus();
        $this->logger->info('open_orchestra_log.status.update', array('status_name' => $status->getName()));
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
}
