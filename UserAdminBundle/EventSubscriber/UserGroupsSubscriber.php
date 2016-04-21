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
            if (false === $user->isSuperAdmin()) {
                $form->add('groups', 'oo_group_choice', array(
                    'multiple' => true,
                    'expanded' => true,
                    'required' => false,
                    'label' => 'open_orchestra_user.form.user.groups'
                ));
            } else {
                $form->add('help_text', 'button', array(
                    'disabled' => true,
                    'label' => 'open_orchestra_user_admin.form.super_admin_help_text'
                ));
            }
        }
    }
}
