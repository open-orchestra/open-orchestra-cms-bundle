<?php

namespace OpenOrchestra\LogBundle\Test\EventSubscriber;

use OpenOrchestra\UserBundle\GroupEvents;
use Phake;
use OpenOrchestra\LogBundle\EventSubscriber\LogGroupSubscriber;

/**
 * Class LogGroupSubscriberTest
 */
class LogGroupSubscriberTest extends LogAbstractSubscriberTest
{
    protected $groupEvent;
    protected $group;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->group = Phake::mock('OpenOrchestra\BackofficeBundle\Document\Group');
        $this->groupEvent = Phake::mock('OpenOrchestra\UserBundle\Event\GroupEvent');
        Phake::when($this->groupEvent)->getGroup()->thenReturn($this->group);

        $this->subscriber = new LogGroupSubscriber($this->logger);
    }

    /**
     * @return array
     */
    public function provideSubscribedEvent()
    {
        return array(
            array(GroupEvents::GROUP_CREATE),
            array(GroupEvents::GROUP_DELETE),
            array(GroupEvents::GROUP_UPDATE),
        );
    }

    /**
     * Test groupCreate
     */
    public function testGroupCreate()
    {
        $this->subscriber->groupCreate($this->groupEvent);
        $this->assertEventLogged('open_orchestra_log.group.create', array('group_name' => $this->group->getName()));
    }

    /**
     * Test groupDelete
     */
    public function testGroupDelete()
    {
        $this->subscriber->groupDelete($this->groupEvent);
        $this->assertEventLogged('open_orchestra_log.group.delete', array('group_name' => $this->group->getName()));
    }

    /**
     * Test groupUpdate
     */
    public function testGroupUpdate()
    {
        $this->subscriber->groupUpdate($this->groupEvent);
        $this->assertEventLogged('open_orchestra_log.group.update', array('group_name' => $this->group->getName()));
    }
}
