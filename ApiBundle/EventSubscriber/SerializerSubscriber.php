<?php

namespace PHPOrchestra\ApiBundle\EventSubscriber;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class SerializerSubscriber
 */
class SerializerSubscriber implements EventSubscriberInterface
{
    protected $resolver;
    protected $serializer;
    protected $annotationReader;

    /**
     * @param Serializer         $serializer
     * @param AnnotationReader   $annotationReader
     * @param ControllerResolver $resolver
     */
    public function __construct(Serializer $serializer, AnnotationReader $annotationReader, ControllerResolver $resolver)
    {
        $this->resolver = $resolver;
        $this->serializer = $serializer;
        $this->annotationReader = $annotationReader;
    }

    /**
     * Serialize Action response with annotation @Serialize
     *
     * @param FilterResponseEvent|GetResponseForControllerResultEvent $event
     */
    public function onKernelViewSerialize(GetResponseForControllerResultEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        if (!$this->isApiRequest($event->getRequest())) {
            return;
        }

        $controller = $this->resolver->getController($event->getRequest());
        $reflectionClass = new \ReflectionClass($controller[0]);
        $annot = $this->annotationReader->getMethodAnnotation($reflectionClass->getMethod($controller[1]), 'PHPOrchestra\ApiBundle\Controller\Annotation\Serialize');

        if (!$annot) {
            return;
        }

        $event->setResponse(
            new Response(
                $this->serializer->serialize(
                    $event->getControllerResult(),
                    $event->getRequest()->get('_format', 'json')),
                200,
                array('content-type' => 'application/json')));
    }

    /**
     * @param Request $request
     *
     * @return boolean
     */
    protected function isApiRequest(Request $request)
    {
        return 0 === strpos($request->get('_route'), 'php_orchestra_api');
    }

    /**
     * @{inherit}
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => 'onKernelViewSerialize',
        );
    }
}
