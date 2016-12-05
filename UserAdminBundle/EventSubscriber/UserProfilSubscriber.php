<?php

namespace OpenOrchestra\UserAdminBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;

/**
 * Class UserProfilSubscriber
 */
class UserProfilSubscriber implements EventSubscriberInterface
{
    protected $allowedToSetPlatformAdmin;
    protected $allowedToSetDeveloper;
    protected $objectManager;

    /**
     * @param UserInterface $user
     * @param ObjectManager $objectManager
     */
    public function __construct(UserInterface $user, ObjectManager $objectManager)
    {
        $this->allowedToSetPlatformAdmin = $user->hasRole(ContributionRoleInterface::PLATFORM_ADMIN) || $user->hasRole(ContributionRoleInterface::DEVELOPER);
        $this->allowedToSetDeveloper = $user->hasRole(ContributionRoleInterface::DEVELOPER);
        $this->objectManager = $objectManager;
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

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $user = $event->getData();

        if ($user instanceof UserInterface) {
            if ($this->allowedToSetPlatformAdmin) {
                $form->add('platform_admin', 'checkbox', array(
                    'data' => $user->hasRole(ContributionRoleInterface::PLATFORM_ADMIN),
                    'value' => true,
                    'label' => 'open_orchestra_user_admin.form.user.platform_admin',
                    'mapped' => false,
                    'required' => false,
                    'group_id' => 'information',
                    'sub_group_id' => 'profil',
                ));
            }
            if ($this->allowedToSetDeveloper) {
                $form->add('developer', 'checkbox', array(
                    'data' => $user->hasRole(ContributionRoleInterface::DEVELOPER),
                    'value' => true,
                    'label' => 'open_orchestra_user_admin.form.user.developer',
                    'mapped' => false,
                    'required' => false,
                    'group_id' => 'information',
                    'sub_group_id' => 'profil',
                ));
            }
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $user = $event->getForm()->getData();
        if ($user instanceof UserInterface) {
            if ($this->allowedToSetPlatformAdmin && array_key_exists('platform_admin', $data) && $data['platform_admin']) {
                $user->addRole(ContributionRoleInterface::PLATFORM_ADMIN);
            }
            if ($this->allowedToSetDeveloper && array_key_exists('developer', $data) && $data['developer']) {
                $user->addRole(ContributionRoleInterface::DEVELOPER);
            }
        }
        $this->objectManager->flush($user);
    }
}
