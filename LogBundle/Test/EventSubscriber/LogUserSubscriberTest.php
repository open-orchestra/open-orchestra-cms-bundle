<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogUserSubscriber;
use PHPOrchestra\UserBundle\UserEvents;

/**
 * Class LogUserSubscriberTest
 */
class LogUserSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogUserSubscriber
     */
    protected $subscriber;

    protected $userEvent;
    protected $logger;
    protected $user;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->user = Phake::mock('PHPOrchestra\UserBundle\Document\User');
        $this->userEvent = Phake::mock('FOS\UserBundle\Event\UserEvent');
        Phake::when($this->userEvent)->getUser()->thenReturn($this->user);
        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
        $this->subscriber = new LogUserSubscriber($this->logger);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * @param string $eventName
     *
     * @dataProvider provideSubscribedEvent
     */
    public function testEventSubscribed($eventName)
    {
        $this->assertArrayHasKey($eventName, $this->subscriber->getSubscribedEvents());
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
        $this->eventTest('php_orchestra_log.user.create');
    }

    /**
     * Test userDelete
     */
    public function testUserDelete()
    {
        $this->subscriber->userDelete($this->userEvent);
        $this->eventTest('php_orchestra_log.user.delete');
    }

    /**
     * Test userUpdate
     */
    public function testUserUpdate()
    {
        $this->subscriber->userUpdate($this->userEvent);
        $this->eventTest('php_orchestra_log.user.update');
    }

    /**
     * Test the userEvent
     *
     * @param string $message
     */
    public function eventTest($message)
    {
        Phake::verify($this->userEvent)->getUser();
        Phake::verify($this->logger)->info($message, array('user_name' => $this->user->getUsername()));
    }
}
