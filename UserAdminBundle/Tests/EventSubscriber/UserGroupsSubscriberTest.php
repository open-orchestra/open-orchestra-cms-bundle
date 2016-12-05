<?php

namespace OpenOrchestra\UserAdminBundle\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\UserAdminBundle\EventSubscriber\UserGroupsSubscriber;
use Symfony\Component\Form\FormEvents;
use Phake;

/**
 * Class UserGroupsSubscriberTest
 */
class UserGroupsSubscriberTest extends AbstractBaseTestCase
{

    protected $event;
    protected $user;
    protected $form;
    protected $subscriber;

    /**
     * Set up common test part
     */
    public function setUp()
    {
        $this->user = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($this->user)->getGroups()->thenReturn(array());

        $this->form = Phake::mock('Symfony\Component\Form\FormInterface');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');

        Phake::when($this->event)->getData()->thenReturn($this->user);
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->subscriber = new UserGroupsSubscriber($this->user);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test subscribed events
     */
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test pre set data with super admin user
     */
    public function testPreSetData()
    {
        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form)->add('groups', 'oo_group_list', array(
            'label' => 'open_orchestra_user_admin.form.user.groups',
            'allowed_sites' => array(),
            'group_id' => 'information',
            'sub_group_id' => 'group',
            'required' => false,
        ));
    }
}
