<?php

namespace OpenOrchestra\UserAdminBundle\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\UserBundle\Repository\UserRepository;
use OpenOrchestra\UserBundle\GroupEvents;
use OpenOrchestra\UserBundle\Event\GroupEvent;

/**
 * Class DeleteGroupSubscriber
 */
class DeleteGroupSubscriber implements EventSubscriberInterface
{
    protected $objectManager;
    protected $userRepository;

    /**
     * @param ObjectManager  $objectManager
     * @param UserRepository $userRepository
     */
    public function __construct(ObjectManager $objectManager, UserRepository $userRepository)
    {
        $this->objectManager = $objectManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @param GroupEvent $event
     */
    public function deleteGroupReference(GroupEvent $event)
    {
        $group = $event->getGroup();
        $users = $this->userRepository->findByGroup($group);
        foreach ($users as $user) {
            $user->removeGroup($group);
            $this->objectManager->flush($user);
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            GroupEvents::GROUP_DELETE => 'deleteGroupReference',
        );
    }
}
