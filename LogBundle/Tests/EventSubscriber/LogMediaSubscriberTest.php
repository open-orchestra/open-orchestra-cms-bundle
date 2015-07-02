<?php

namespace OpenOrchestra\LogBundle\Tests\EventSubscriber;

use Phake;
use OpenOrchestra\LogBundle\EventSubscriber\LogMediaSubscriber;
use OpenOrchestra\Media\FolderEvents;
use OpenOrchestra\Media\MediaEvents;

/**
 * Class LogMediaSubscriberTest
 */
class LogMediaSubscriberTest extends LogAbstractSubscriberTest
{
    protected $media;
    protected $folder;
    protected $mediaEvent;
    protected $folderEvent;
    protected $mediaContext;
    protected $folderContext;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->media = Phake::mock('OpenOrchestra\Media\Model\MediaInterface');
        $this->mediaEvent = Phake::mock('OpenOrchestra\Media\Event\MediaEvent');
        Phake::when($this->mediaEvent)->getMedia()->thenReturn($this->media);
        $this->folder = Phake::mock('OpenOrchestra\Media\Model\FolderInterface');
        $this->folderEvent = Phake::mock('OpenOrchestra\Media\Event\FolderEvent');
        Phake::when($this->folderEvent)->getFolder()->thenReturn($this->folder);

        $this->mediaContext = array('media_name' => $this->media->getName());
        $this->folderContext = array('folder_name' => $this->folder->getName());

        $this->subscriber = new LogMediaSubscriber($this->logger);
    }

    /**
     * @return array
     */
    public function provideSubscribedEvent()
    {
        return array(
            array(MediaEvents::ADD_IMAGE),
            array(MediaEvents::MEDIA_CROP),
            array(MediaEvents::MEDIA_DELETE),
            array(FolderEvents::FOLDER_CREATE),
            array(FolderEvents::FOLDER_DELETE),
            array(FolderEvents::FOLDER_UPDATE),
        );
    }

    /**
     * Test add image
     */
    public function testAddImage()
    {
        $this->subscriber->mediaAddImage($this->mediaEvent);
        $this->assertEventLogged('open_orchestra_log.media.add_image', $this->mediaContext);
    }

    /**
     * Test Delete
     */
    public function testDelete()
    {
        $this->subscriber->mediaDelete($this->mediaEvent);
        $this->assertEventLogged('open_orchestra_log.media.delete', $this->mediaContext);
    }

    /**
     * Test Delete
     */
    public function testResize()
    {
        $this->subscriber->mediaResize($this->mediaEvent);
        $this->assertEventLogged('open_orchestra_log.media.resize', $this->mediaContext);
    }

    /**
     * test folderCreate
     */
    public function testFolderCreate()
    {
        $this->subscriber->folderCreate($this->folderEvent);
        $this->assertEventLogged('open_orchestra_log.folder.create', $this->folderContext);
    }

    /**
     * test folderDelete
     */
    public function testFolderDelete()
    {
        $this->subscriber->folderDelete($this->folderEvent);
        $this->assertEventLogged('open_orchestra_log.folder.delete', $this->folderContext);
    }

    /**
     * test folderUpdate
     */
    public function testFolderUpdate()
    {
        $this->subscriber->folderUpdate($this->folderEvent);
        $this->assertEventLogged('open_orchestra_log.folder.update', $this->folderContext);
    }
}
