<?php

namespace PHPOrchestra\LogBundle\EventSubscriber;

use PHPOrchestra\ModelInterface\ContentTypeEvents;
use PHPOrchestra\ModelInterface\Event\ContentTypeEvent;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogContentTypeSubscriber
 */
class LogContentTypeSubscriber implements EventSubscriberInterface
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
     * @param ContentTypeEvent $event
     */
    public function contentTypeCreation(ContentTypeEvent $event)
    {
        $contentType = $event->getContentType();
        $this->logger->info('php_orchestra_log.content_type.create', array(
            'content_type_id' => $contentType->getContentTypeId(),
        ));
    }

    /**
     * @param ContentTypeEvent $event
     */
    public function contentTypeDelete(ContentTypeEvent $event)
    {
        $contentType = $event->getContentType();
        $this->logger->info('php_orchestra_log.content_type.delete', array(
            'content_type_id' => $contentType->getContentTypeId(),
            'content_type_name' => $contentType->getName()
        ));
    }

    /**
     * @param ContentTypeEvent $event
     */
    public function contentTypeUpdate(ContentTypeEvent $event)
    {
        $contentType = $event->getContentType();
        $this->logger->info('php_orchestra_log.content_type.update', array(
            'content_type_id' => $contentType->getContentTypeId(),
            'content_type_name' => $contentType->getName()
        ));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentTypeEvents::CONTENT_TYPE_CREATE => 'contentTypeCreation',
            ContentTypeEvents::CONTENT_TYPE_DELETE => 'contentTypeDelete',
            ContentTypeEvents::CONTENT_TYPE_UPDATE => 'contentTypeUpdate',
        );
    }
}
