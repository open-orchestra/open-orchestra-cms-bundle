<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Event\TrashcanEvent;
use OpenOrchestra\ModelInterface\TrashcanEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\ModelInterface\BlockEvents;
use OpenOrchestra\ModelInterface\Event\BlockEvent;
use OpenOrchestra\Backoffice\Reference\ReferenceManager;
use OpenOrchestra\ModelInterface\ContentTypeEvents;
use OpenOrchestra\ModelInterface\Event\ContentTypeEvent;

/**
 * Class UpdateReferenceSubscriber
 */
class UpdateReferenceSubscriber implements EventSubscriberInterface
{
    protected $referenceManager;

    /**
     * @param ReferenceManager $referenceManager
     */
    public function __construct(ReferenceManager $referenceManager)
    {
        $this->referenceManager = $referenceManager;
    }

    /**
     * @param BlockEvent $event
     */
    public function updateReferencesToBlock(BlockEvent $event)
    {
        $block = $event->getBlock();
        $this->referenceManager->updateReferencesToEntity($block);
    }

    /**
     * @param BlockEvent $event
     */
    public function removeReferencesToBlock(BlockEvent $event)
    {
        $block = $event->getBlock();
        $this->referenceManager->removeReferencesToEntity($block);
    }

    /**
     * @param ContentEvent $event
     */
    public function updateReferencesToContent(ContentEvent $event)
    {
        $content = $event->getContent();
        $this->referenceManager->updateReferencesToEntity($content);
    }

    /**
     * @param ContentTypeEvent $event
     */
    public function updateReferencesToContentType(ContentTypeEvent $event)
    {
        $contentType = $event->getContentType();
        $this->referenceManager->updateReferencesToEntity($contentType);
    }

    /**
     * @param TrashcanEvent $event
     */
    public function removeReferencesToEntity(TrashcanEvent $event)
    {
        $deletedElement = $event->getDeletedEntity();
        $this->referenceManager->removeReferencesToEntity($deletedElement);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            BlockEvents::POST_BLOCK_CREATE => 'updateReferencesToBlock',
            BlockEvents::POST_BLOCK_UPDATE => 'updateReferencesToBlock',
            BlockEvents::POST_BLOCK_DELETE => 'removeReferencesToBlock',
            ContentEvents::CONTENT_UPDATE => 'updateReferencesToContent',
            ContentEvents::CONTENT_CREATION => 'updateReferencesToContent',
            ContentEvents::CONTENT_DUPLICATE => 'updateReferencesToContent',
            ContentTypeEvents::CONTENT_TYPE_CREATE => 'updateReferencesToContentType',
            ContentTypeEvents::CONTENT_TYPE_UPDATE => 'updateReferencesToContentType',
            TrashcanEvents::TRASHCAN_REMOVE_ENTITY => 'removeReferencesToEntity',
        );
    }
}
