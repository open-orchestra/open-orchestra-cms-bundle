<?php

namespace OpenOrchestra\UserAdminBundle\EventSubscriber;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\GroupBundle\Event\GroupFacadeEvent;
use OpenOrchestra\GroupBundle\GroupFacadeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class GroupUserListTransformSubscriber
 */
class GroupUserListTransformSubscriber implements EventSubscriberInterface
{
    protected $router;
    protected $authorizationChecker;

    /**
     * @param UrlGeneratorInterface         $router
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(UrlGeneratorInterface $router, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param GroupFacadeEvent $event
     */
    public function postGroupTransform(GroupFacadeEvent $event)
    {
        $facade = $event->getGroupFacade();
        $group = $event->getGroup();

        if ($this->authorizationChecker->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_UPDATE_USER)) {
            $facade->addLink('_self_panel_user_list', $this->router->generate(
                'open_orchestra_api_user_list_by_group',
                array('groupId' => $group->getId())
            ));
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            GroupFacadeEvents::POST_GROUP_TRANSFORMATION => array('postGroupTransform', 10),
        );
    }
}
