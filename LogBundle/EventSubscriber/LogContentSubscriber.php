<?php

namespace PHPOrchestra\LogBundle\EventSubscriber;

use PHPOrchestra\ModelInterface\ContentEvents;
use PHPOrchestra\ModelInterface\Event\ContentEvent;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogContentSubscriber
 */
class LogContentSubscriber implements EventSubscriberInterface
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
     * @param ContentEvent $event
     */
    public function contentCreation(ContentEvent $event)
    {
        $content = $event->getContent();
        $this->logger->info('php_orchestra_log.content.create', array('content_id' => $content->getContentId(), 'content_name' => $content->getName()));
    }

    /**
     * @param ContentEvent $event
     */
    public function contentDelete(ContentEvent $event)
    {
        $content = $event->getContent();
        $this->logger->info('php_orchestra_log.content.delete', array('content_id' => $content->getContentId(), 'content_name' => $content->getName()));
    }

    /**
     * @param ContentEvent $event
     */
    public function contentUpdate(ContentEvent $event)
    {
        $content = $event->getContent();
        $this->logger->info('php_orchestra_log.content.update', array('content_id' => $content->getContentId(), 'content_name' => $content->getName()));
    }

    /**
     * @param ContentEvent $event
     */
    public function contentDuplicate(ContentEvent $event)
    {
        $content = $event->getContent();
        $this->logger->info('php_orchestra_log.content.duplicate', array(
            'content_id' => $content->getContentId(),
            'content_name' => $content->getName(),
            'content_version' => $content->getVersion()
        ));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentEvents::CONTENT_DUPLICATE => 'contentDuplicate',
            ContentEvents::CONTENT_CREATION => 'contentCreation',
            ContentEvents::CONTENT_DELETE => 'contentDelete',
            ContentEvents::CONTENT_UPDATE => 'contentUpdate',
        );
    }
}
