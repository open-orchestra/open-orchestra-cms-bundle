<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\EventSubscriber;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use OpenOrchestra\UserAdminBundle\Event\UserFacadeEvent;
use OpenOrchestra\UserAdminBundle\UserFacadeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AddSubmitButtonSubscriber
 */
class AddWorkFlowLinkSubscriber implements EventSubscriberInterface
{
    protected $router;

    /**
     * @param UrlGeneratorInterface $router
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param UserFacadeEvent $event
     */
    public function postUserTransformation(UserFacadeEvent $event)
    {
        $facade = $event->getUserFacade();
        $facade->addLink('_self_panel_2_workflow_right',
            $this->router->generate('open_orchestra_backoffice_workflow_right_form',
            array('userId' => $facade->id),
            UrlGeneratorInterface::ABSOLUTE_URL));
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            UserFacadeEvents::POST_USER_TRANSFORMATION => 'postUserTransformation',
        );
    }
}
