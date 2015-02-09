<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogStatusSubscriber;
use PHPOrchestra\ModelInterface\StatusEvents;

/**
 * Class LogStatusSubscriberTest
 */
class LogStatusSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogStatusSubscriber
     */
    protected $subscriber;

    protected $logger;
    protected $status;
    protected $statusElement;
    protected $statusableEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->status = Phake::mock('PHPOrchestra\ModelBundle\Document\Status');
        $this->statusElement = Phake::mock('PHPOrchestra\ModelInterface\Model\StatusableInterface');
        Phake::when($this->statusElement)->getStatus()->thenReturn($this->status);
        $this->statusableEvent = Phake::mock('PHPOrchestra\ModelInterface\Event\StatusableEvent');
        Phake::when($this->statusableEvent)->getStatusableElement()->thenReturn($this->statusElement);
        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
        $this->subscriber = new LogStatusSubscriber($this->logger);
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
        $this->eventTest('php_orchestra_log.status.create');
    }

    /**
     * Test statusDelete
     */
    public function testStatusDelete()
    {
        $this->subscriber->statusDelete($this->statusableEvent);
        $this->eventTest('php_orchestra_log.status.delete');
    }

    /**
     * Test statusUpdate
     */
    public function testStatusUpdate()
    {
        $this->subscriber->statusUpdate($this->statusableEvent);
        $this->eventTest('php_orchestra_log.status.update');
    }

    /**
     * Test the statusableEvent
     */
    public function eventTest($message)
    {
        Phake::verify($this->statusableEvent)->getStatusableElement();
        Phake::verify($this->logger)->info($message, array('status_name' => $this->status->getName()));
    }
}
