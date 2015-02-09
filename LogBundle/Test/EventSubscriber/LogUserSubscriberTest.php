<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogUserSubscriber;
use PHPOrchestra\UserBundle\UserEvents;

/**
 * Class LogUserSubscriberTest
 */
class LogUserSubscriberTest extends LogAbstractSubscriberTest
{
    protected $userEvent;
    protected $user;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->user = Phake::mock('PHPOrchestra\UserBundle\Document\User');
        $this->userEvent = Phake::mock('FOS\UserBundle\Event\UserEvent');
        Phake::when($this->userEvent)->getUser()->thenReturn($this->user);

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
        $this->assertEventLogged('php_orchestra_log.user.create', array(
            'user_name' => $this->user->getUsername(),
        ));
    }

    /**
     * Test userDelete
     */
    public function testUserDelete()
    {
        $this->subscriber->userDelete($this->userEvent);
        $this->assertEventLogged('php_orchestra_log.user.delete', array(
            'user_name' => $this->user->getUsername(),
        ));
    }

    /**
     * Test userUpdate
     */
    public function testUserUpdate()
    {
        $this->subscriber->userUpdate($this->userEvent);
        $this->assertEventLogged('php_orchestra_log.user.update', array(
            'user_name' => $this->user->getUsername(),
        ));
    }
}
