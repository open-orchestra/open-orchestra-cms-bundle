<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogContentTypeSubscriber;
use PHPOrchestra\ModelInterface\ContentTypeEvents;

/**
 * Class LogContentTypeSubscriberTest
 */
class LogContentTypeSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogContentTypeSubscriber
     */
    protected $subscriber;

    protected $logger;
    protected $contentType;
    protected $contentTypeEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->contentType = Phake::mock('PHPOrchestra\ModelBundle\Document\ContentType');
        $this->contentTypeEvent = Phake::mock('PHPOrchestra\ModelInterface\Event\ContentTypeEvent');
        Phake::when($this->contentTypeEvent)->getContentType()->thenReturn($this->contentType);
        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
        $this->subscriber = new LogContentTypeSubscriber($this->logger);
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
            array(ContentTypeEvents::CONTENT_TYPE_CREATE),
            array(ContentTypeEvents::CONTENT_TYPE_DELETE),
            array(ContentTypeEvents::CONTENT_TYPE_UPDATE),
        );
    }

    /**
     * test contentTypeCreation
     */
    public function testContentTypeCreation()
    {
        $this->subscriber->contentTypeCreation($this->contentTypeEvent);
        $this->eventTest();
    }

    /**
     * test contentTypeDelete
     */
    public function testContentTypeDelete()
    {
        $this->subscriber->contentTypeDelete($this->contentTypeEvent);
        $this->eventTest();
    }

    /**
     * test contentTypeUpdate
     */
    public function testContentTypeUpdate()
    {
        $this->subscriber->contentTypeUpdate($this->contentTypeEvent);
        $this->eventTest();
    }

    /**
     * Test the contentTypeEvent
     */
    public function eventTest()
    {
        Phake::verify($this->contentTypeEvent)->getContentType();
        Phake::verify($this->logger)->info(Phake::anyParameters());
        Phake::verify($this->contentType)->getContentTypeId();
    }
}
