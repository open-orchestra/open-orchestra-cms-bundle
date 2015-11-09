<?php

namespace OpenOrchestra\UserAdminBundle\Tests\EventSubscriber;

use OpenOrchestra\UserAdminBundle\EventSubscriber\DeleteGroupSubscriber;
use OpenOrchestra\UserBundle\GroupEvents;
use Doctrine\Common\Collections\ArrayCollection;

use Phake;

/**
 * Class DeleteGroupSubscriberTest
 */
abstract class DeleteGroupSubscriberTest extends \PHPUnit_Framework_TestCase
{

    protected $objectManager;
    protected $event;
    protected $group;
    protected $users;
    protected $subscriber;

    /**
     * Set up common test part
     */
    public function setUp()
    {
        $this->users = new ArrayCollection();
        $user0 = Phake::mock('FOS\UserBundle\Model\UserInterface');
        $user1 = Phake::mock('FOS\UserBundle\Model\UserInterface');
        $this->users->add($user0);
        $this->users->add($user1);

        $userRepository = Phake::mock('OpenOrchestra\UserBundle\Repository\UserRepository');
        $this->objectManager = Phake::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->event = Phake::mock('OpenOrchestra\UserBundle\Event\GroupEvent');
        $this->group = Phake::mock('FOS\UserBundle\Model\GroupInterface');

        Phake::when($this->event)->getGroup()->thenReturn($this->group);
        Phake::when($userRepository)->findBy(\Phake::anyParameters())->thenReturn($this->users);

        $this->subscriber = new DeleteGroupSubscriber($this->objectManager, $this->userRepository);
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
        $this->assertArrayHasKey(GroupEvents::GROUP_DELETE, $this->subscriber->getSubscribedEvents());
    }

    /**
     * test deleteGroupReference
     */
    public function testDeleteGroupReference()
    {
        $this->subscriber->deleteGroupReference($this->event);

        foreach ($this->users as $user) {
            Phake::verify($user)->removeGroup($this->group);
            Phake::verify($this->objectManager)->flush($user);
        }
    }
}
