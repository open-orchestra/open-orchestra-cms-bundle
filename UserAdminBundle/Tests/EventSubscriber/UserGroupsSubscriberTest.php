<?php

namespace OpenOrchestra\UserAdminBundle\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\UserAdminBundle\EventSubscriber\UserGroupsSubscriber;
use Symfony\Component\Form\FormEvents;
use Phake;

/**
 * Class UserGroupsSubscriberTest
 */
abstract class UserGroupsSubscriberTest extends AbstractBaseTestCase
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
        $this->form = Phake::mock('Symfony\Component\Form\FormInterface');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvents');

        Phake::when($this->event)->getData()->thenReturn($this->user);
        Phake::when($this->event)->getForm()->thenReturn($this->form);

        $this->subscriber = new UserGroupsSubscriber();
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
    public function testPreSetDataWithSuperAdmin()
    {
        Phake::when($this->user)->isSuperAdmin()->thenReturn(true);
        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form)->add('help_text', 'button', array(
            'disabled' => true,
            'label' => 'open_orchestra_user_admin.form.super_admin_help_text'
        ));
    }

    /**
     * Test test pre set data without super admin user
     */
    public function testPreSetDataWithoutSuperAdmin()
    {
        Phake::when($this->user)->isSuperAdmin->thenReturn(false);
        $this->subscriber->preSetData($this->event);

        Phake::verify($this->form)->add('groups', 'oo_group_choice', array(
            'multiple' => true,
            'expanded' => true,
            'required' => false,
        ));
    }
}
