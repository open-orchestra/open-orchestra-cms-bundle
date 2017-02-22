<?php

namespace OpenOrchestra\GroupBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OpenOrchestra\UserBundle\Repository\UserRepositoryInterface;
use OpenOrchestra\Backoffice\Model\GroupInterface;

/**
 * Class GroupMemberSubscriber
 */
class GroupMemberSubscriber implements EventSubscriberInterface
{
    protected $userRepository;

    /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $group = $event->getData();
        $form = $event->getForm();

        if ($group instanceof GroupInterface) {
            $users = $this->userRepository->findUsersByGroups($group->getId());
            $members = array();
            foreach ($users as $user) {
                $members[$user->getId()] = array('member' => true);
            }
            $form->add('members', 'oo_member_list', array(
                'label' => false,
                'data' => $members,
                'mapped' => false,
                'group_id' => 'member',
                'required' => false
            ));
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        if (($group = $event->getForm()->getData()) instanceof GroupInterface) {
            $data = $event->getData();
            $members = (array_key_exists('members', $data) && array_key_exists('members_collection', $data['members'])) ? array_keys($data['members']['members_collection']) : array();
            $this->userRepository->removeGroupFromNotListedUsers($group->getId(), $members);
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }
}
