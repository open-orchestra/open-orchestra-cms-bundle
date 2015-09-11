<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;

/**
 * Class DeleteContentSubscriber
 */
class DeleteContentSubscriber extends  AbstractDeleteSubscriber
{
    /**
     * @param ContentEvent $event
     */
    public function addContentTrashCan(ContentEvent $event)
    {
        $content = $event->getContent();
        $this->createTrashItem($content, $content->getName());
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentEvents::CONTENT_DELETE => 'addContentTrashCan',
        );
    }
}
