<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\RedirectionEvent;
use OpenOrchestra\ModelInterface\RedirectionEvents;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogRedirectionSubscriber
 */
class LogRedirectionSubscriber implements EventSubscriberInterface
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
     * @param RedirectionEvent $event
     */
    public function redirectionCreate(RedirectionEvent $event)
    {
        $redirection = $event->getRedirection();
        $this->logger->info('open_orchestra_log.redirection.create', array(
            'redirection_pattern' => $redirection->getRoutePattern(),
            'redirection_site_name' => $redirection->getSiteName()
        ));
    }

    /**
     * @param RedirectionEvent $event
     */
    public function redirectionDelete(RedirectionEvent $event)
    {
        $redirection = $event->getRedirection();
        $this->logger->info('open_orchestra_log.redirection.delete', array(
            'redirection_pattern' => $redirection->getRoutePattern(),
            'redirection_site_name' => $redirection->getSiteName()
        ));
    }

    /**
     * @param RedirectionEvent $event
     */
    public function redirectionUpdate(RedirectionEvent $event)
    {
        $redirection = $event->getRedirection();
        $this->logger->info('open_orchestra_log.redirection.update', array(
            'redirection_pattern' => $redirection->getRoutePattern(),
            'redirection_site_name' => $redirection->getSiteName()
        ));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            RedirectionEvents::REDIRECTION_CREATE => 'redirectionCreate',
            RedirectionEvents::REDIRECTION_DELETE => 'redirectionDelete',
            RedirectionEvents::REDIRECTION_UPDATE => 'redirectionUpdate',
        );
    }
}
