<?php

namespace PHPOrchestra\LogBundle\EventSubscriber;

use PHPOrchestra\Media\Event\MediaEvent;
use PHPOrchestra\Media\MediaEvents;
use PHPOrchestra\Media\Event\FolderEvent;
use PHPOrchestra\Media\FolderEvents;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogMediaSubscriber
 */
class LogMediaSubscriber implements EventSubscriberInterface
{
    protected $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param MediaEvent $event
     */
    public function mediaAddImage(MediaEvent $event)
    {
        $media = $event->getMedia();
        $this->logger->info('Create a new media', array('media_folder' => $media->getMediaFolder(), 'media_name' => $media->getName()));
    }

    /**
     * @param MediaEvent $event
     */
    public function mediaDelete(MediaEvent $event)
    {
        $media = $event->getMedia();
        $this->logger->info('Delete a media', array('media_folder' => $media->getMediaFolder(), 'media_name' => $media->getName()));
    }

    /**
     * @param MediaEvent $event
     */
    public function mediaResize(MediaEvent $event)
    {
        $media = $event->getMedia();
        $this->logger->info('Resize a media', array('media_folder' => $media->getMediaFolder(), 'media_name' => $media->getName()));
    }

    /**
     * @param MediaEvent $event
     */
    public function mediaOverride(MediaEvent $event)
    {
        $media = $event->getMedia();
        $this->logger->info('Override a media', array('media_folder' => $media->getMediaFolder(), 'media_name' => $media->getName()));
    }

    /**
     * @param FolderEvent $event
     */
    public function folderCreate(FolderEvent $event)
    {
        $folder = $event->getFolder();
        $this->logger->info('Create a new folder', array('folder_name' => $folder->getName()));
    }

    /**
     * @param FolderEvent $event
     */
    public function folderDelete(FolderEvent $event)
    {
        $folder = $event->getFolder();
        $this->logger->info('Delete a folder', array('folder_name' => $folder->getName()));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            MediaEvents::ADD_IMAGE => 'mediaAddImage',
            MediaEvents::RESIZE_IMAGE => 'mediaResize',
            MediaEvents::MEDIA_DELETE => 'mediaDelete',
            FolderEvents::FOLDER_CREATE => 'folderCreate',
            FolderEvents::FOLDER_DELETE => 'folderDelete',
            MediaEvents::OVERRIDE_IMAGE => 'mediaOverride',
        );
    }
}
