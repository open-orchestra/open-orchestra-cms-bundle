<?php

namespace PHPOrchestra\LogBundle\EventSubscriber;

use PHPOrchestra\Media\Event\ImagickEvent;
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
        $this->logger->info('php_orchestra_log.media.add_image', array('media_name' => $media->getName()));
    }

    /**
     * @param MediaEvent $event
     */
    public function mediaDelete(MediaEvent $event)
    {
        $media = $event->getMedia();
        $this->logger->info('php_orchestra_log.media.delete', array('media_name' => $media->getName()));
    }

    /**
     * @param ImagickEvent $event
     */
    public function mediaResize(ImagickEvent $event)
    {
        $media = $event->getFileName();
        $this->logger->info('php_orchestra_log.media.resize', array('media_name' => $media));
    }

    /**
     * @param ImagickEvent $event
     */
    public function mediaOverride(ImagickEvent $event)
    {
        $media = $event->getFileName();
        $this->logger->info('php_orchestra_log.media.override', array('media_name' => $media));
    }

    /**
     * @param FolderEvent $event
     */
    public function folderCreate(FolderEvent $event)
    {
        $folder = $event->getFolder();
        $this->logger->info('php_orchestra_log.folder.create', array('folder_name' => $folder->getName()));
    }

    /**
     * @param FolderEvent $event
     */
    public function folderDelete(FolderEvent $event)
    {
        $folder = $event->getFolder();
        $this->logger->info('php_orchestra_log.folder.delete', array('folder_name' => $folder->getName()));
    }

    /**
     * @param FolderEvent $event
     */
    public function folderUpdate(FolderEvent $event)
    {
        $folder = $event->getFolder();
        $this->logger->info('php_orchestra_log.folder.update', array('folder_name' => $folder->getName()));
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
            FolderEvents::FOLDER_UPDATE => 'folderUpdate',
            MediaEvents::OVERRIDE_IMAGE => 'mediaOverride',
        );
    }
}
