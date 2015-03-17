<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\DisplayBundle\Manager\CacheableManager;

/**
 * Class ChangeContentStatusSubscriber
 */
class ChangeContentStatusSubscriber implements EventSubscriberInterface
{
    protected $cacheableManager;

    /**
     * @param CacheableManager $cacheableManager
     */
    public function __construct(CacheableManager $cacheableManager)
    {
        $this->cacheableManager = $cacheableManager;
    }

    public function contentChangeStatus(ContentEvent $event)
    {
        $content = $event->getContent();
        $this->cacheableManager->invalidateTags(array('contentId-' . $content->getContentId()));
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
