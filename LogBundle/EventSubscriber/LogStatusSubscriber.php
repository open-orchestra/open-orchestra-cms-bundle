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

    public function statusChange(StatusableEvent $event)
    {
        $this->logger->info('', array());
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            StatusEvents::STATUS_CHANGE => 'statusEvent',
        );
    }
}
