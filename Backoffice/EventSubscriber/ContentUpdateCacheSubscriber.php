<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\DisplayBundle\Manager\CacheableManager;
use OpenOrchestra\BaseBundle\Manager\TagManager;

/**
 * Class ContentUpdateCacheSubscriber
 */
class ContentUpdateCacheSubscriber implements EventSubscriberInterface
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
     * @param ContentEvent $event
     */
    public function invalidateCacheOnStatusChanged(ContentEvent $event)
    {
        $content = $event->getContent();
        $previousStatus = $event->getPreviousStatus();
        if ($previousStatus instanceof StatusInterface) {
            $this->invalidateTagsContent($content, $previousStatus);
        }
    }

   /**
     * @param ContentEvent $event
     */
    public function invalidateCacheOnDeleteContentPublished(ContentEvent $event)
    {
        $content = $event->getContent();
        $status = $content->getStatus();
        if ($status instanceof StatusInterface) {
            $this->invalidateTagsContent($content, $status);
        }
    }

    /**
     * @param ContentInterface $content
     * @param StatusInterface  $status
     */
    protected function invalidateTagsContent(ContentInterface $content, StatusInterface $status)
    {
        if ($status->isPublished()) {
            $this->cacheableManager->invalidateTags(
                array(
                    $this->tagManager->formatContentIdTag($content->getContentId())
                )
            );
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentEvents::CONTENT_CHANGE_STATUS => 'invalidateCacheOnStatusChanged',
            ContentEvents::CONTENT_DELETE => 'invalidateCacheOnDeleteContentPublished'
        );
    }
}
