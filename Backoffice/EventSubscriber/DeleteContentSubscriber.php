<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentDeleteEvent;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Model\ContentInterface;

/**
 * Class DeleteContentSubscriber
 */
class DeleteContentSubscriber extends  AbstractDeleteSubscriber
{
    /**
     * @param ContentDeleteEvent $event
     */
    public function addContentTrashCan(ContentDeleteEvent $event)
    {
        $name = $event->getContentId();
        $type = ContentInterface::TRASH_ITEM_TYPE;
        $this->createTrashItem($event->getContentId(), $event->getSiteId(), $name, $type);
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
