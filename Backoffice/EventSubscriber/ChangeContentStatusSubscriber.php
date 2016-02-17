<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\DisplayBundle\Manager\CacheableManager;
use OpenOrchestra\BaseBundle\Manager\TagManager;

/**
 * Class ChangeContentStatusSubscriber
 */
class ChangeContentStatusSubscriber implements EventSubscriberInterface
{
    protected $cacheableManager;
    protected $tagManager;

    /**
     * @param CacheableManager $cacheableManager
     * @param TagManager       $tagManager
     */
    public function __construct(CacheableManager $cacheableManager, TagManager $tagManager)
    {
        $this->cacheableManager = $cacheableManager;
        $this->tagManager = $tagManager;
    }

    /**
     * Triggered when a content status changes
     * 
     * @param ContentEvent $event
     */
    public function contentChangeStatus(ContentEvent $event)
    {
        $content = $event->getContent();

        $this->cacheableManager->invalidateTags(
            array(
                $this->tagManager->formatContentIdTag($content->getContentId())
            )
        );
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentEvents::CONTENT_CHANGE_STATUS => 'contentChangeStatus'
        );
    }
}
