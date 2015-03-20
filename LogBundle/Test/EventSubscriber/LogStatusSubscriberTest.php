<?php

namespace OpenOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use OpenOrchestra\LogBundle\EventSubscriber\LogStatusSubscriber;
use OpenOrchestra\ModelInterface\StatusEvents;

/**
 * Class LogStatusSubscriberTest
 */
class LogStatusSubscriberTest extends LogAbstractSubscriberTest
{
    protected $status;
    protected $statusEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->status = Phake::mock('OpenOrchestra\ModelInterface\Model\StatusInterface');
        $this->statusEvent = Phake::mock('OpenOrchestra\ModelInterface\Event\StatusEvent');
        Phake::when($this->statusEvent)->getStatus()->thenReturn($this->status);

        $this->subscriber = new LogStatusSubscriber($this->logger);
    }

    /**
     * @return array
     */
    public function provideSubscribedEvent()
    {
        return array(
            array(StatusEvents::STATUS_CREATE),
            array(StatusEvents::STATUS_DELETE),
            array(StatusEvents::STATUS_UPDATE),
        );
    }

    /**
     * Test statusCreate
     */
    public function testStatusCreate()
    {
        $this->subscriber->statusCreate($this->statusEvent);
        $this->assertEventLogged('open_orchestra_log.status.create', array(
            'status_name' => $this->status->getName()
        ));
    }

    /**
     * Test statusDelete
     */
    public function testStatusDelete()
    {
        $this->subscriber->statusDelete($this->statusEvent);
        $this->assertEventLogged('open_orchestra_log.status.delete', array(
            'status_name' => $this->status->getName()
        ));
    }

    /**
     * Test statusUpdate
     */
    public function testStatusUpdate()
    {
        $this->subscriber->statusUpdate($this->statusEvent);
        $this->assertEventLogged('open_orchestra_log.status.update', array(
            'status_name' => $this->status->getName()
        ));
    }
}
