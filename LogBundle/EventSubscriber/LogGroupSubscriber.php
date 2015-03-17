<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\UserBundle\Event\GroupEvent;
use OpenOrchestra\UserBundle\GroupEvents;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogGroupSubscriber
 */
class LogGroupSubscriber implements EventSubscriberInterface
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
     * @param GroupEvent $event
     */
    public function groupCreate(GroupEvent $event)
    {
        $group = $event->getGroup();
        $this->logger->info('open_orchestra_log.group.create', array('group_name' => $group->getName()));
    }

    /**
     * @param GroupEvent $event
     */
    public function groupDelete(GroupEvent $event)
    {
        $group = $event->getGroup();
        $this->logger->info('open_orchestra_log.group.delete', array('group_name' => $group->getName()));
    }

    /**
     * @param GroupEvent $event
     */
    public function groupUpdate(GroupEvent $event)
    {
        $group = $event->getGroup();
        $this->logger->info('open_orchestra_log.group.update', array('group_name' => $group->getName()));
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
}
