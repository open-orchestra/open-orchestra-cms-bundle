<?php

namespace PHPOrchestra\ApiBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class SerializerSubscriber
 */
class SerializerSubscriber implements EventSubscriberInterface
{
    public function onKernelViewSerialize(GetResponseForControllerResultEvent $event)
    {
        //TODO modify response
    }

    /**
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => 'onKernelViewSerialize',
        );
    }
}
