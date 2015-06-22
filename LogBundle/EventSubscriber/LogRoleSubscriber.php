<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\RoleEvent;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\ModelInterface\RoleEvents;

/**
 * Class LogRoleSubscriber
 */
class LogRoleSubscriber extends AbstractLogSubscriber
{
    /**
     * @param RoleEvent $event
     */
    public function roleCreate(RoleEvent $event)
    {
        $this->sendLog('open_orchestra_log.role.create', $event->getRole());
    }

    /**
     * @param RoleEvent $event
     */
    public function roleDelete(RoleEvent $event)
    {
        $this->sendLog('open_orchestra_log.role.delete', $event->getRole());
    }

    /**
     * @param RoleEvent $event
     */
    public function roleUpdate(RoleEvent $event)
    {
        $this->sendLog('open_orchestra_log.role.update', $event->getRole());
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

    /**
     * @param string        $message
     * @param RoleInterface $role
     */
    protected function sendLog($message, RoleInterface $role)
    {
        $this->logger->info($message, array('role_name' => $role->getName()));
    }
}
