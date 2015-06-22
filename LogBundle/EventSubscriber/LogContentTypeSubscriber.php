<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\ContentTypeEvents;
use OpenOrchestra\ModelInterface\Event\ContentTypeEvent;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;

/**
 * Class LogContentTypeSubscriber
 */
class LogContentTypeSubscriber extends AbstractLogSubscriber
{
    /**
     * @param ContentTypeEvent $event
     */
    public function contentTypeCreation(ContentTypeEvent $event)
    {
        $this->sendLog('open_orchestra_log.content_type.create', $event->getContentType());
    }

    /**
     * @param ContentTypeEvent $event
     */
    public function contentTypeDelete(ContentTypeEvent $event)
    {
        $this->sendLog('open_orchestra_log.content_type.delete', $event->getContentType());
    }

    /**
     * @param ContentTypeEvent $event
     */
    public function contentTypeUpdate(ContentTypeEvent $event)
    {
        $this->sendLog('open_orchestra_log.content_type.update', $event->getContentType());
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

    /**
     * @param string               $message
     * @param ContentTypeInterface $contentType
     */
    protected function sendLog($message, ContentTypeInterface $contentType)
    {
        $this->logger->info($message, array(
            'content_type_id' => $contentType->getContentTypeId(),
            'content_type_name' => $contentType->getName()
        ));
    }
}
