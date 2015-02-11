<?php

namespace PHPOrchestra\LogBundle\Test\EventSubscriber;

use Phake;
use PHPOrchestra\LogBundle\EventSubscriber\LogMediaSubscriber;
use PHPOrchestra\Media\FolderEvents;
use PHPOrchestra\Media\MediaEvents;

/**
 * Class LogMediaSubscriberTest
 */
class LogMediaSubscriberTest extends LogAbstractSubscriberTest
{
    protected $media;
    protected $folder;
    protected $mediaEvent;
    protected $folderEvent;

    /**
     * Set up the test
     */
    public function setUp()
    {
        parent::setUp();
        $this->media = Phake::mock('PHPOrchestra\MediaBundle\Document\Media');
        $this->mediaEvent = Phake::mock('PHPOrchestra\Media\Event\MediaEvent');
        Phake::when($this->mediaEvent)->getMedia()->thenReturn($this->media);
        $this->folder = Phake::mock('PHPOrchestra\MediaBundle\Document\Folder');
        $this->folderEvent = Phake::mock('PHPOrchestra\Media\Event\FolderEvent');
        Phake::when($this->folderEvent)->getFolder()->thenReturn($this->folder);

        $this->subscriber = new LogMediaSubscriber($this->logger);
    }

    /**
     * @return array
     */
    public function provideSubscribedEvent()
    {
        return array(
            array(MediaEvents::ADD_IMAGE),
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
        $this->assertEventLogged('php_orchestra_log.media.add_image', array(
            'media_name' => $this->media->getName()
        ));
    }

    /**
     * Test Delete
     */
    public function testDelete()
    {
        $this->subscriber->mediaDelete($this->mediaEvent);
        $this->assertEventLogged('php_orchestra_log.media.delete', array(
            'media_name' => $this->media->getName()
        ));
    }

    /**
     * test folderCreate
     */
    public function testFolderCreate()
    {
        $this->subscriber->folderCreate($this->folderEvent);
        $this->assertEventLogged('php_orchestra_log.folder.create', array(
            'folder_name' => $this->folder->getName()
        ));
    }

    /**
     * test folderDelete
     */
    public function testFolderDelete()
    {
        $this->subscriber->folderDelete($this->folderEvent);
        $this->assertEventLogged('php_orchestra_log.folder.delete', array(
            'folder_name' => $this->folder->getName()
        ));
    }

    /**
     * test folderUpdate
     */
    public function testFolderUpdate()
    {
        $this->subscriber->folderUpdate($this->folderEvent);
        $this->assertEventLogged('php_orchestra_log.folder.update', array(
            'folder_name' => $this->folder->getName()
        ));
    }
}
