<?php

namespace PHPOrchestra\LogBundle\EventSubscriber;

use PHPOrchestra\BackofficeBundle\Event\NodeEvent;
use PHPOrchestra\BackofficeBundle\NodeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogSubscriber
 */
class LogSubscriber implements EventSubscriberInterface
{
    /**
     * @param NodeEvent $event
     */
    public function nodeCreation(NodeEvent $event)
    {

    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            NodeEvents::NODE_CREATION => 'nodeEvent',
        );
    }

}
