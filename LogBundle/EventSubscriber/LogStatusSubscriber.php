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
    public function statusChange(StatusableEvent $event)
    {
        $status = $event->getStatusableElement();
        $this->logger->info('', array());
    }

    /**
     * @param StatusableEvent $event
     */
    public function statusCreate(StatusableEvent $event)
    {
        $status = $event->getStatusableElement();
        $this->logger->info('', array());
    }

    /**
     * @param StatusableEvent $event
     */
    public function statusDelete(StatusableEvent $event)
    {
        $status = $event->getStatusableElement();
        $this->logger->info('', array());
    }

    /**
     * @param StatusableEvent $event
     */
    public function statusUpdate(StatusableEvent $event)
    {
        $status = $event->getStatusableElement();
        $this->logger->info('', array());
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
