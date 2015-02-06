<?php

namespace PHPOrchestra\LogBundle\EventSubscriber;

use FOS\UserBundle\Event\UserEvent;
use PHPOrchestra\UserBundle\UserEvents;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Class LogUserSubscriber
 */
class LogUserSubscriber implements EventSubscriberInterface
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
     * @param UserEvent $event
     */
    public function userCreate(UserEvent $event)
    {
        $user = $event->getUser();
        $this->logger->info('php_orchestra_log.user.create', array(
            'user_name' => $user->getUsername(),
        ));
    }

    /**
     * @param UserEvent $event
     */
    public function userDelete(UserEvent $event)
    {
        $user = $event->getUser();
        $this->logger->info('php_orchestra_log.user.delete', array(
            'user_name' => $user->getUsername(),
        ));
    }

    /**
     * @param UserEvent $event
     */
    public function userUpdate(UserEvent $event)
    {
        $user = $event->getUser();
        $this->logger->info('php_orchestra_log.user.update', array(
            'user_name' => $user->getUsername(),
        ));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            UserEvents::USER_CREATE => 'userCreate',
            UserEvents::USER_DELETE => 'userDelete',
            UserEvents::USER_UPDATE => 'userUpdate',
        );
    }
}
