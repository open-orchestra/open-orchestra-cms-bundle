<?php

namespace OpenOrchestra\GroupBundle\Tests\EventSubscriber;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use OpenOrchestra\GroupBundle\EventSubscriber\GroupMemberSubscriber;
use Phake;
use Symfony\Component\Form\FormEvents;

/**
 * Class GroupMemberSubscriberTest
 */
class GroupMemberSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var GroupMemberSubscriber
     */
    protected $subscriber;
    protected $userRepository;
    protected $event;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->userRepository = Phake::mock('OpenOrchestra\UserBundle\Repository\UserRepositoryInterface');
        $this->event = Phake::mock('Symfony\Component\Form\FormEvent');

        $this->subscriber = new GroupMemberSubscriber($this->userRepository);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test event subscribed
     */
    public function testEventSubscribed()
    {
        $this->assertArrayHasKey(FormEvents::PRE_SET_DATA, $this->subscriber->getSubscribedEvents());
        $this->assertArrayHasKey(FormEvents::PRE_SUBMIT, $this->subscriber->getSubscribedEvents());
    }

    /**
     * Test preSetData
     */
    public function testPreSetData()
    {
        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($this->event)->getData()->thenReturn($group);
        $form = Phake::mock('Symfony\Component\Form\FormInterface');
        Phake::when($this->event)->getForm()->thenReturn($form);

        $user0 = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($user0)->getId()->thenReturn('fakeUser0Id');
        $user1 = Phake::mock('OpenOrchestra\UserBundle\Model\UserInterface');
        Phake::when($user1)->getId()->thenReturn('fakeUser1Id');

        $users = array($user0, $user1);

        Phake::when($this->userRepository)->findUsersByGroups(Phake::anyParameters())->thenReturn($users);

        $this->subscriber->preSetData($this->event);

        Phake::verify($form)->add('members', 'oo_member_list', array(
                'data' => array(
                    'fakeUser0Id' => array('member' => true),
                    'fakeUser1Id' => array('member' => true)
                ),
                'mapped' => false,
                'group_id' => 'member',
            ));
    }

    /**
     * Test preSubmit
     */
    public function testPreSubmit()
    {
        $form = Phake::mock('Symfony\Component\Form\FormInterface');

        $group = Phake::mock('OpenOrchestra\Backoffice\Model\GroupInterface');
        Phake::when($group)->getId()->thenReturn('fakeGroupId');
        Phake::when($form)->getData()->thenReturn($group);
        Phake::when($this->event)->getForm()->thenReturn($form);
        Phake::when($this->event)->getData()->thenReturn(array('members' => array('members_collection' => array('fakeMemberId' => true))));

        $this->subscriber->preSubmit($this->event);

        Phake::verify($this->userRepository)->removeGroupFromNotListedUsers('fakeGroupId', array('fakeMemberId'));
    }
}
