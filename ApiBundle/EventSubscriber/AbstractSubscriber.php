<?php

namespace OpenOrchestra\ApiBundle\EventSubscriber;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\KernelEvent;

/**
 * Class AbstractSubscriber
 */
abstract class AbstractSubscriber
{
    protected $resolver;
    protected $annotationReader;

    /**
     * @param AnnotationReader            $annotationReader
     * @param ControllerResolverInterface $resolver
     */
    public function __construct(AnnotationReader $annotationReader, ControllerResolverInterface $resolver)
    {
        $this->resolver = $resolver;
        $this->annotationReader = $annotationReader;
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
     * @param KernelEvent $event
     * @param string      $annotationClassName
     *
     * @return null|object
     */
    protected function extractAnnotation(KernelEvent $event, $annotationClassName)
    {
        $controller = $this->resolver->getController($event->getRequest());
        $reflectionClass = new \ReflectionClass($controller[0]);
        $annot = $this->annotationReader->getMethodAnnotation($reflectionClass->getMethod(
            $controller[1]),
            $annotationClassName
        );
        return $annot;
    }

    /**
     * @param KernelEvent $event
     *
     * @return bool
     */
    protected function eventElligible(KernelEvent $event)
    {
        if (! $event->isMasterRequest()) {
            return false;
        }

        if (!$this->isApiRequest($event->getRequest())) {
            return false;
        }

        return true;
    }
}
