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
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
class SerializerSubscriber extends AbstractSubscriber implements EventSubscriberInterface
{
    protected $serializer;

    /**
     * @param SerializerInterface         $serializer
     * @param AnnotationReader            $annotationReader
     * @param ControllerResolverInterface $resolver
     */
    public function __construct(SerializerInterface $serializer, AnnotationReader $annotationReader, ControllerResolverInterface $resolver)
    {
        parent::__construct($annotationReader, $resolver);
        $this->serializer = $serializer;
    }

    /**
     * Serialize Action response with annotation @Serialize
     *
     * @param FilterResponseEvent|GetResponseForControllerResultEvent $event
     */
    public function onKernelViewSerialize(GetResponseForControllerResultEvent $event)
    {
        if (!$this->eventElligible($event)) {
            return;
        }

        $annot = $this->extractAnnotation($event, 'OpenOrchestra\ApiBundle\Controller\Annotation\Serialize');

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
