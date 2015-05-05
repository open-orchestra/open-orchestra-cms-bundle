<?php

namespace OpenOrchestra\ApiBundle\EventSubscriber;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\ApiException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class HttpExceptionSubscriber
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
class HttpExceptionSubscriber extends ContainerAware implements EventSubscriberInterface
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!($exception = $event->getException()) instanceof  ApiException ) {
            return;
        }

        $request = $event->getRequest();
        $attributes = array(
            '_controller' => 'OpenOrchestra\ApiBundle\Controller\ExceptionController::showAction',
            'exception' => $exception,
            'format' => $request->getRequestFormat('json'),
        );
        $request = $request->duplicate(null, null, $attributes);
        $request->setMethod('GET');

        $response = $event->getKernel()->handle($request, HttpKernelInterface::SUB_REQUEST, true);
        $event->setResponse($response);
        $event->stopPropagation();
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => array('onKernelException', 1000),
        );
    }
}
