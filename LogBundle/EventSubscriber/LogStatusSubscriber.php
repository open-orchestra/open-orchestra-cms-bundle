<?php

namespace PHPOrchestra\LogBundle\EventSubscriber;

use PHPOrchestra\ModelInterface\Event\StatusableEvent;
use PHPOrchestra\ModelInterface\StatusEvents;
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
     * @param StatusableEvent $event
     */
    public function statusCreate(StatusableEvent $event)
    {
        $status = $event->getStatusableElement();
        $this->logger->info('php_orchestra_log.status.create', array('status_name' => $status->getStatus()->getName()));
    }

    /**
     * @param StatusableEvent $event
     */
    public function statusDelete(StatusableEvent $event)
    {
        $status = $event->getStatusableElement();
        $this->logger->info('php_orchestra_log.status.delete', array('status_name' => $status->getStatus()->getName()));
    }

    /**
     * @param StatusableEvent $event
     */
    public function statusUpdate(StatusableEvent $event)
    {
        $status = $event->getStatusableElement();
        $this->logger->info('php_orchestra_log.status.update', array('status_name' => $status->getStatus()->getName()));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            StatusEvents::STATUS_CHANGE => 'statusChange',
            StatusEvents::STATUS_CREATE => 'statusCreate',
            StatusEvents::STATUS_DELETE => 'statusDelete',
            StatusEvents::STATUS_UPDATE => 'statusUpdate',
        );
    }
}
