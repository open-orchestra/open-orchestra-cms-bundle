<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Model\ContentInterface;

/**
 * Class LogContentSubscriber
 */
class LogContentSubscriber extends AbstractLogSubscriber
{
    /**
     * @param ContentEvent $event
     */
    public function contentCreation(ContentEvent $event)
    {
        $content = $event->getContent();
        $this->logger->info('open_orchestra_log.content.create', array(
            'content_id' => $content->getContentId(),
        ));
    }

    /**
     * @param ContentEvent $event
     */
    public function contentDelete(ContentEvent $event)
    {
        $content = $event->getContent();
        $this->logger->info('open_orchestra_log.content.delete', array(
            'content_id' => $content->getContentId(),
            'content_name' => $content->getName(),
        ));
    }

    /**
     * @param ContentEvent $event
     */
    public function contentUpdate(ContentEvent $event)
    {
        $this->sendLog('open_orchestra_log.content.update', $event->getContent());
    }

    /**
     * @param ContentEvent $event
     */
    public function contentDuplicate(ContentEvent $event)
    {
        $this->sendLog('open_orchestra_log.content.duplicate', $event->getContent());
    }

    /**
     * @param ContentEvent $event
     */
    public function contentChangeStatus(ContentEvent $event)
    {
        $this->sendLog('open_orchestra_log.content.status', $event->getContent());
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentEvents::CONTENT_CHANGE_STATUS => 'contentChangeStatus',
            ContentEvents::CONTENT_DUPLICATE => 'contentDuplicate',
            ContentEvents::CONTENT_CREATION => 'contentCreation',
            ContentEvents::CONTENT_DELETE => 'contentDelete',
            ContentEvents::CONTENT_UPDATE => 'contentUpdate',
        );
    }

    /**
     * @param string           $message
     * @param ContentInterface $content
     */
    protected function sendLog($message, $content)
    {
        $this->logger->info($message, array(
            'content_id' => $content->getContentId(),
            'content_version' => $content->getVersion(),
            'content_language' => $content->getLanguage()
        ));
    }
}
