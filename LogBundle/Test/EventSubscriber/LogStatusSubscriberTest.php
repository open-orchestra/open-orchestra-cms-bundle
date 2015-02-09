<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogStatusSubscriber;
use PHPOrchestra\ModelInterface\StatusEvents;

/**
 * Class LogStatusSubscriberTest
 */
class LogStatusSubscriberTest extends LogAbstractSubscriberTest
{
    protected $status;
    protected $statusElement;
    protected $statusableEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->status = Phake::mock('PHPOrchestra\ModelBundle\Document\Status');
        $this->statusElement = Phake::mock('PHPOrchestra\ModelInterface\Model\StatusableInterface');
        Phake::when($this->statusElement)->getStatus()->thenReturn($this->status);
        $this->statusableEvent = Phake::mock('PHPOrchestra\ModelInterface\Event\StatusableEvent');
        Phake::when($this->statusableEvent)->getStatusableElement()->thenReturn($this->statusElement);

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
        $this->subscriber->statusCreate($this->statusableEvent);
        $this->assertEventLogged('php_orchestra_log.status.create', array(
            'status_name' => $this->status->getName()
        ));
    }

    /**
     * Test statusDelete
     */
    public function testStatusDelete()
    {
        $this->subscriber->statusDelete($this->statusableEvent);
        $this->assertEventLogged('php_orchestra_log.status.delete', array(
            'status_name' => $this->status->getName()
        ));
    }

    /**
     * Test statusUpdate
     */
    public function testStatusUpdate()
    {
        $this->subscriber->statusUpdate($this->statusableEvent);
        $this->assertEventLogged('php_orchestra_log.status.update', array(
            'status_name' => $this->status->getName()
        ));
    }
}
