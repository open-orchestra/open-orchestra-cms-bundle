<?php

namespace OpenOrchestra\LogBundle\Tests\EventSubscriber;

use Phake;
use OpenOrchestra\LogBundle\EventSubscriber\LogUserSubscriber;
use OpenOrchestra\UserBundle\UserEvents;

/**
 * Class LogUserSubscriberTest
 */
class LogUserSubscriberTest extends LogAbstractSubscriberTest
{
    protected $userEvent;
    protected $context;
    protected $user;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->user = Phake::mock('OpenOrchestra\UserBundle\Document\User');
        $this->userEvent = Phake::mock('OpenOrchestra\UserBundle\Event\UserEvent');
        Phake::when($this->userEvent)->getUser()->thenReturn($this->user);
        $this->context = array('user_name' => $this->user->getUsername());

        $this->subscriber = new LogUserSubscriber($this->logger);
    }

    /**
     * @return array
     */
    public function provideSubscribedEvent()
    {
        return array(
            array(UserEvents::USER_CREATE),
            array(UserEvents::USER_DELETE),
            array(UserEvents::USER_UPDATE),
        );
    }

    /**
     * Test userCreate
     */
    public function testUserCreate()
    {
        $this->subscriber->userCreate($this->userEvent);
        $this->assertEventLogged('open_orchestra_log.user.create', $this->context);
    }

    /**
     * Test userDelete
     */
    public function testUserDelete()
    {
        $this->subscriber->userDelete($this->userEvent);
        $this->assertEventLogged('open_orchestra_log.user.delete', $this->context);
    }

    /**
     * Test userUpdate
     */
    public function testUserUpdate()
    {
        $this->subscriber->userUpdate($this->userEvent);
        $this->assertEventLogged('open_orchestra_log.user.update', $this->context);
    }

    /**
     * Test userUpdate
     */
    public function testUserLogin()
    {
        $userInterface = Phake::mock('Symfony\Component\Security\Core\User\UserInterface');
        $token = Phake::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        Phake::when($token)->getUser()->thenReturn($userInterface);
        $interactiveEvent = Phake::mock('Symfony\Component\Security\Http\Event\InteractiveLoginEvent');
        Phake::when($interactiveEvent)->getAuthenticationToken()->thenReturn($token);
        $this->subscriber->userLogin($interactiveEvent);
        $this->assertEventLogged('open_orchestra_log.user.login', array(
            'user_name' => $userInterface->getUsername(),
        ));
    }
}
