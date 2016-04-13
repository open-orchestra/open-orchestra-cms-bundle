<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\DisplayBundle\Manager\CacheableManager;
use OpenOrchestra\BaseBundle\Manager\TagManager;

@trigger_error('The '.__NAMESPACE__.'\ChangeContentStatusSubscriber class is deprecated since version 1.1.0 and will be removed in 1.2.0, it is replace by ContentUpdateCacheSubscriber', E_USER_DEPRECATED);

/**
 * Class ChangeContentStatusSubscriber
 *
 * @deprecated ChangeContentStatusSubscriber is deprecated in 1.1.0 and will be removed in 1.2.0, it is replace by ContentUpdateCacheSubscriber
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
        dump($event->getPreviousStatus());

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
