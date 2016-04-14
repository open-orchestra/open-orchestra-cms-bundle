<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\ContentTypeEvents;
use OpenOrchestra\ModelInterface\Event\ContentTypeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\DisplayBundle\Manager\CacheableManager;
use OpenOrchestra\BaseBundle\Manager\TagManager;

/**
 * Class ContentTypeUpdateCacheSubscriber
 */
class ContentTypeUpdateCacheSubscriber implements EventSubscriberInterface
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
     * @param ContentTypeEvent $event
     */
    public function invalidateTagContentType(ContentTypeEvent $event)
    {
        $contentType = $event->getContentType();
        $this->cacheableManager->invalidateTags(
            array(
                $this->tagManager->formatContentTypeTag($contentType->getContentTypeId())
            )
        );
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentTypeEvents::CONTENT_TYPE_UPDATE => 'invalidateTagContentType',
            ContentTypeEvents::CONTENT_TYPE_DELETE => 'invalidateTagContentType',
        );
    }
}
