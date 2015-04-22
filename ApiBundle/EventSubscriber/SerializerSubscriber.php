<?php

namespace OpenOrchestra\ApiBundle\EventSubscriber;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
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
     * @param SerializerInterface         $serializer
     * @param AnnotationReader            $annotationReader
     * @param ControllerResolverInterface $resolver
     */
    public function __construct(SerializerInterface $serializer, AnnotationReader $annotationReader, ControllerResolverInterface $resolver)
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
        $annot = $this->annotationReader->getMethodAnnotation($reflectionClass->getMethod($controller[1]), 'OpenOrchestra\ApiBundle\Controller\Annotation\Serialize');

        if (!$annot) {
            return;
        }

        $format = $event->getRequest()->get('_format', 'json');
        $event->setResponse(
            new Response(
                $this->serializer->serialize(
                    $event->getControllerResult(),
                    $format),
                200,
                array('content-type' => $this->generateContentType($format))));
    }

    /**
     * @param Request $request
     *
     * @return boolean
     */
    protected function isApiRequest(Request $request)
    {
        return 0 === strpos($request->get('_route'), 'open_orchestra_api');
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

    /**
     * @param string $format
     *
     * @return string
     */
    protected function generateContentType($format)
    {
        switch ($format) {
            case 'json':
                return 'application/json';
            case 'xml' :
                return 'text/xml';
            case 'yml':
                return 'application/yaml';
            default :
                return 'text/html';
        }
    }
}
