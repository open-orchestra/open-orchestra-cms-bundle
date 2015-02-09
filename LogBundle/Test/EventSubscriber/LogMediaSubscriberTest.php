<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogMediaSubscriber;
use PHPOrchestra\Media\MediaEvents;

/**
 * Class LogMediaSubscriberTest
 */
class LogMediaSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogMediaSubscriber
     */
    protected $subscriber;

    protected $media;
    protected $folder;
    protected $logger;
    protected $mediaEvent;
    protected $folderEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->media = Phake::mock('PHPOrchestra\MediaBundle\Document\Media');
        $this->mediaEvent = Phake::mock('PHPOrchestra\Media\Event\MediaEvent');
        Phake::when($this->mediaEvent)->getMedia()->thenReturn($this->media);
        $this->folder = Phake::mock('PHPOrchestra\MediaBundle\Document\Folder');
        $this->folderEvent = Phake::mock('PHPOrchestra\Media\Event\FolderEvent');
        Phake::when($this->folderEvent)->getFolder()->thenReturn($this->folder);

        $this->logger = Phake::mock('Symfony\Bridge\Monolog\Logger');
        $this->subscriber = new LogMediaSubscriber($this->logger);
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
            array(MediaEvents::ADD_IMAGE),
            array(MediaEvents::MEDIA_DELETE),
            array(MediaEvents::OVERRIDE_IMAGE),
            array(MediaEvents::RESIZE_IMAGE),
        );
    }

    /**
     * Test add image
     */
    public function testAddImage()
    {
        $this->subscriber->mediaAddImage($this->mediaEvent);
        $this->eventMediaTest();
    }

    /**
     * Test Delete
     */
    public function testDelete()
    {
        $this->subscriber->mediaDelete($this->mediaEvent);
        $this->eventMediaTest();
    }

    /**
     * Test the Media event
     */
    public function eventMediaTest()
    {
        Phake::verify($this->mediaEvent)->getMedia();
        Phake::verify($this->logger)->info(Phake::anyParameters());
        Phake::verify($this->media)->getName();
    }

    /**
     * test folderCreate
     */
    public function testFolderCreate()
    {
        $this->subscriber->folderCreate($this->folderEvent);
        $this->eventFolderTest();
    }

    /**
     * test folderDelete
     */
    public function testFolderDelete()
    {
        $this->subscriber->folderDelete($this->folderEvent);
        $this->eventFolderTest();
    }

    /**
     * test folderUpdate
     */
    public function testFolderUpdate()
    {
        $this->subscriber->folderUpdate($this->folderEvent);
        $this->eventFolderTest();
    }

    /**
     * Test the folder Event
     */
    public function eventFolderTest()
    {
        Phake::verify($this->folderEvent)->getFolder();
        Phake::verify($this->logger)->info(Phake::anyParameters());
        Phake::verify($this->folder)->getName();
    }
}
