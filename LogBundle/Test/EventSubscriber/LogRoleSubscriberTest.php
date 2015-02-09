<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogRoleSubscriber;
use PHPOrchestra\ModelInterface\RoleEvents;

/**
 * Class LogRoleSubscriberTest
 */
class LogRoleSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogRoleSubscriber
     */
    protected $subscriber;

    protected $roleEvent;
    protected $logger;
    protected $role;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->role = Phake::mock('PHPOrchestra\ModelBundle\Document\Role');
        $this->roleEvent = Phake::mock('PHPOrchestra\ModelInterface\Event\RoleEvent');
        Phake::when($this->roleEvent)->getRole()->thenReturn($this->role);
        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
        $this->subscriber = new LogRoleSubscriber($this->logger);
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
            array(RoleEvents::ROLE_CREATE),
            array(RoleEvents::ROLE_DELETE),
            array(RoleEvents::ROLE_UPDATE),
        );
    }

    /**
     * Test roleCreate
     */
    public function testRoleCreate()
    {
        $this->subscriber->roleCreate($this->roleEvent);
        $this->eventTest();
    }

    /**
     * Test roleDelete
     */
    public function testRoleDelete()
    {
        $this->subscriber->roleDelete($this->roleEvent);
        $this->eventTest();
    }

    /**
     * Test roleUpdate
     */
    public function testRoleUpdate()
    {
        $this->subscriber->roleUpdate($this->roleEvent);
        $this->eventTest();
    }

    /**
     * Test the roleEvent
     */
    public function eventTest()
    {
        Phake::verify($this->roleEvent)->getRole();
        Phake::verify($this->logger)->info(Phake::anyParameters());
        Phake::verify($this->role)->getName();
    }
}
