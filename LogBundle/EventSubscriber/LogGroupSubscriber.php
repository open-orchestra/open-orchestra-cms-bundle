<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use FOS\UserBundle\Model\GroupInterface;
use OpenOrchestra\UserBundle\Event\GroupEvent;
use OpenOrchestra\UserBundle\GroupEvents;

/**
 * Class LogGroupSubscriber
 */
class LogGroupSubscriber extends AbstractLogSubscriber
{
    /**
     * @param GroupEvent $event
     */
    public function groupCreate(GroupEvent $event)
    {
        $this->sendLog('open_orchestra_log.group.create', $event->getGroup());
    }

    /**
     * @param GroupEvent $event
     */
    public function groupDelete(GroupEvent $event)
    {
        $this->sendLog('open_orchestra_log.group.delete', $event->getGroup());
    }

    /**
     * @param GroupEvent $event
     */
    public function groupUpdate(GroupEvent $event)
    {
        $this->sendLog('open_orchestra_log.group.update', $event->getGroup());
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            GroupEvents::GROUP_CREATE => 'groupCreate',
            GroupEvents::GROUP_DELETE => 'groupDelete',
            GroupEvents::GROUP_UPDATE => 'groupUpdate',
        );
    }

    /**
     * @param string         $message
     * @param GroupInterface $group
     */
    protected function sendLog($message, GroupInterface $group)
    {
        $this->logger->info($message, array('group_name' => $group->getName()));
    }
}
