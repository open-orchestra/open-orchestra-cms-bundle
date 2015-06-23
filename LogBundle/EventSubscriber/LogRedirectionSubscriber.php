<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\RedirectionEvent;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;
use OpenOrchestra\ModelInterface\RedirectionEvents;

/**
 * Class LogRedirectionSubscriber
 */
class LogRedirectionSubscriber extends AbstractLogSubscriber
{
    /**
     * @param RedirectionEvent $event
     */
    public function redirectionCreate(RedirectionEvent $event)
    {
        $this->sendLog('open_orchestra_log.redirection.create', $event->getRedirection());
    }

    /**
     * @param RedirectionEvent $event
     */
    public function redirectionDelete(RedirectionEvent $event)
    {
        $this->sendLog('open_orchestra_log.redirection.delete', $event->getRedirection());
    }

    /**
     * @param RedirectionEvent $event
     */
    public function redirectionUpdate(RedirectionEvent $event)
    {
        $this->sendLog('open_orchestra_log.redirection.update', $event->getRedirection());
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

    /**
     * @param string               $message
     * @param RedirectionInterface $redirection
     */
    protected function sendLog($message, RedirectionInterface $redirection)
    {
        $this->logger->info($message, array(
            'redirection_pattern' => $redirection->getRoutePattern(),
            'redirection_site_name' => $redirection->getSiteName()
        ));
    }
}
