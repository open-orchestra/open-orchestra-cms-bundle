<?php

namespace PHPOrchestra\LogBundle\EventSubscriber;

use PHPOrchestra\ModelInterface\Event\RoleEvent;
use PHPOrchestra\ModelInterface\RoleEvents;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogRoleSubscriber
 */
class LogRoleSubscriber implements EventSubscriberInterface
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
     * @param RoleEvent $event
     */
    public function roleCreate(RoleEvent $event)
    {
        $role = $event->getRole();
        $this->logger->info('php_orchestra_log.role.create', array('role_name' => $role->getName()));
    }

    /**
     * @param RoleEvent $event
     */
    public function roleDelete(RoleEvent $event)
    {
        $role = $event->getRole();
        $this->logger->info('php_orchestra_log.role.delete', array('role_name' => $role->getName()));
    }

    /**
     * @param RoleEvent $event
     */
    public function roleUpdate(RoleEvent $event)
    {
        $role = $event->getRole();
        $this->logger->info('php_orchestra_log.role.update', array('role_name' => $role->getName()));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            RoleEvents::ROLE_CREATE => 'roleCreate',
            RoleEvents::ROLE_DELETE => 'roleDelete',
            RoleEvents::ROLE_UPDATE => 'roleUpdate',
        );
    }
}
