<?php

namespace PHPOrchestra\LogBundle\EventSubscriber;

use FOS\UserBundle\Event\UserEvent;
use PHPOrchestra\UserBundle\UserEvents;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
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
        $this->info('php_orchestra_log.user.create', $user->getUsername());
    }

    /**
     * @param UserEvent $event
     */
    public function userDelete(UserEvent $event)
    {
        $user = $event->getUser();
        $this->info('php_orchestra_log.user.delete', $user->getUsername());
    }

    /**
     * @param UserEvent $event
     */
    public function userUpdate(UserEvent $event)
    {
        $user = $event->getUser();
        $this->info('php_orchestra_log.user.update', $user->getUsername());
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function userLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $this->info('php_orchestra_log.user.login', $user->getUserName());
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
            SecurityEvents::INTERACTIVE_LOGIN => 'userLogin'
        );
    }

    /**
     * @param string $message
     * @param string $userName
     */
    protected function info($message, $userName)
    {
        $this->logger->info($message, array(
            'user_name' => $userName
        ));
    }
}
