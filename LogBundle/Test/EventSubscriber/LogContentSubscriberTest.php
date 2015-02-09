<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogContentSubscriber;
use PHPOrchestra\ModelInterface\ContentEvents;

/**
 * Class LogContentSubscriberTest
 */
class LogContentSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogContentSubscriber
     */
    protected $subscriber;

    protected $logger;
    protected $content;
    protected $contentEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->content = Phake::mock('PHPOrchestra\ModelBundle\Document\Content');
        $this->contentEvent = Phake::mock('PHPOrchestra\ModelInterface\Event\ContentEvent');
        Phake::when($this->contentEvent)->getContent()->thenReturn($this->content);
        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
        $this->subscriber = new LogContentSubscriber($this->logger);
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
            array(ContentEvents::CONTENT_CREATION),
            array(ContentEvents::CONTENT_DELETE),
            array(ContentEvents::CONTENT_DUPLICATE),
            array(ContentEvents::CONTENT_UPDATE),
        );
    }

    /**
     * Test contentCreation
     */
    public function testContentCreation()
    {
        $this->subscriber->contentCreation($this->contentEvent);
        $this->eventTest();
    }

    /**
     * Test contentDelete
     */
    public function testContentDelete()
    {
        $this->subscriber->contentDelete($this->contentEvent);
        $this->eventTest();
    }

    /**
     * Test contentUpdate
     */
    public function testContentUpdate()
    {
        $this->subscriber->contentUpdate($this->contentEvent);
        $this->eventTest();
    }

    /**
     * Test contentDuplicate
     */
    public function testContentDuplicate()
    {
        $this->subscriber->contentDuplicate($this->contentEvent);
        $this->eventTest();
    }

    /**
     * Test contentChangeStatus
     */
    public function testContentChangeStatus()
    {
        $this->subscriber->contentChangeStatus($this->contentEvent);
        $this->eventTest();
    }

    /**
     * Test the contentEvent
     */
    public function eventTest()
    {
        Phake::verify($this->contentEvent)->getContent();
        Phake::verify($this->logger)->info(Phake::anyParameters());
        Phake::verify($this->content)->getContentId();
    }
}
