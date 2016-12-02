<?php

namespace OpenOrchestra\UserAdminBundle\EventSubscriber;

use OpenOrchestra\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class UserGroupsSubscriber
 */
class UserGroupsSubscriber implements EventSubscriberInterface
{
    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $user = $event->getData();

        if ($user instanceof UserInterface) {
            $form->add('groups', 'oo_group_list', array(
                'label' => 'open_orchestra_user_admin.form.user.groups',
                'group_id' => 'information',
                'sub_group_id' => 'group',
                'required' => false,
            ));
        }
    }
}
