<?php

namespace OpenOrchestra\UserAdminBundle\EventSubscriber;

use OpenOrchestra\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;

/**
 * Class UserGroupsSubscriber
 */
class UserGroupsSubscriber implements EventSubscriberInterface
{

    protected $allowedSites = null;

    /**
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        if (!$user->hasRole(ContributionRoleInterface::PLATFORM_ADMIN) && !$user->hasRole(ContributionRoleInterface::DEVELOPER)) {
            $this->allowedSites = array();
            foreach ($user->getGroups() as $group) {
                $site = $group->getSite();
                if (!$site->isDeleted() && !in_array($site->getSiteId(), $this->allowedSites)) {
                    $this->allowedSites[] = $site->getSiteId();
                }
            }
        }
    }

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
                'allowed_sites' => $this->allowedSites,
                'group_id' => 'information',
                'sub_group_id' => 'group',
                'required' => false,
            ));
        }
    }
}
