<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AbstractLogSubscriber
 */
abstract class AbstractLogSubscriber implements EventSubscriberInterface
{
    protected $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }
}
