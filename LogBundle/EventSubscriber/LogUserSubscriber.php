<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\UserBundle\Event\UserEvent;
use OpenOrchestra\UserBundle\UserEvents;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Class LogUserSubscriber
 */
class LogUserSubscriber extends AbstractLogSubscriber
{
    /**
     * @param UserEvent $event
     */
    public function userCreate(UserEvent $event)
    {
        $this->sendLog('open_orchestra_log.user.create', $event->getUser());
    }

    /**
     * @param UserEvent $event
     */
    public function userDelete(UserEvent $event)
    {
        $this->sendLog('open_orchestra_log.user.delete', $event->getUser());
    }

    /**
     * @param UserEvent $event
     */
    public function userUpdate(UserEvent $event)
    {
        $this->sendLog('open_orchestra_log.user.update', $event->getUser());
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function userLogin(InteractiveLoginEvent $event)
    {
        $this->sendLog('open_orchestra_log.user.login', $event->getAuthenticationToken()->getUser());
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

    /**
     * @param string        $message
     * @param UserInterface $user
     */
    protected function sendLog($message, UserInterface $user)
    {
        $this->info($message, $user->getUsername());
    }
}
