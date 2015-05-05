<?php

namespace OpenOrchestra\ApiBundle\EventSubscriber;

use Doctrine\Common\Annotations\AnnotationReader;
use OpenOrchestra\ApiBundle\Context\GroupContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class GroupContextSubscriber
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
class GroupContextSubscriber extends AbstractSubscriber implements EventSubscriberInterface
{
    protected $groupContext;

    /**
     * @param GroupContext                $groupContext
     * @param AnnotationReader            $annotationReader
     * @param ControllerResolverInterface $resolver
     */
    public function __construct(GroupContext $groupContext, AnnotationReader $annotationReader, ControllerResolverInterface $resolver)
    {
        parent::__construct($annotationReader, $resolver);
        $this->groupContext = $groupContext;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->eventElligible($event)) {
            return;
        }

        $annot = $this->extractAnnotation($event, 'OpenOrchestra\ApiBundle\Controller\Annotation\Groups');

        if (!$annot) {
            return;
        }

        $this->groupContext->setGroups($annot->groups);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onKernelRequest',
        );
    }
}
