<?php

namespace OpenOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use OpenOrchestra\LogBundle\EventSubscriber\LogRoleSubscriber;
use OpenOrchestra\ModelInterface\RoleEvents;

/**
 * Class LogRoleSubscriberTest
 */
class LogRoleSubscriberTest extends LogAbstractSubscriberTest
{
    protected $roleEvent;
    protected $role;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->role = Phake::mock('OpenOrchestra\ModelBundle\Document\Role');
        $this->roleEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\RoleEvent');
        Phake::when($this->roleEvent)->getRole()->thenReturn($this->role);

        $this->subscriber = new LogRoleSubscriber($this->logger);
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
        $this->assertEventLogged('open_orchestra_log.role.create', array('role_name' => $this->role->getName()));
    }

    /**
     * Test roleDelete
     */
    public function testRoleDelete()
    {
        $this->subscriber->roleDelete($this->roleEvent);
        $this->assertEventLogged('open_orchestra_log.role.delete', array('role_name' => $this->role->getName()));
    }

    /**
     * Test roleUpdate
     */
    public function testRoleUpdate()
    {
        $this->subscriber->roleUpdate($this->roleEvent);
        $this->assertEventLogged('open_orchestra_log.role.update', array('role_name' => $this->role->getName()));
    }
}
