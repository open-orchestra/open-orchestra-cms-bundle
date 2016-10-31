<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Event\TrashcanEvent;
use OpenOrchestra\ModelInterface\TrashcanEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
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
     * @param NodeEvent $event
     */
    public function updateReferencesToNode(NodeEvent $event)
    {
        $node = $event->getNode();
        $this->referenceManager->updateReferencesToEntity($node);
    }

    /**
     * @param ContentEvent $event
     */
    public function updateReferencesToContent(ContentEvent $event)
    {
        var_dump('duplicate');
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
            NodeEvents::NODE_UPDATE_BLOCK => 'updateReferencesToNode',
            NodeEvents::NODE_DUPLICATE => 'updateReferencesToNode',
            NodeEvents::NODE_CREATION => 'updateReferencesToNode',
            NodeEvents::NODE_DELETE_BLOCK => 'updateReferencesToNode',
            NodeEvents::NODE_UPDATE_BLOCK_POSITION => 'updateReferencesToNode',
            NodeEvents::NODE_DUPLICATE => 'updateReferencesToNode',
            ContentEvents::CONTENT_UPDATE => 'updateReferencesToContent',
            ContentEvents::CONTENT_CREATION => 'updateReferencesToContent',
            ContentEvents::CONTENT_DUPLICATE => 'updateReferencesToContent',
            ContentTypeEvents::CONTENT_TYPE_CREATE => 'updateReferencesToContentType',
            ContentTypeEvents::CONTENT_TYPE_UPDATE => 'updateReferencesToContentType',
            TrashcanEvents::TRASHCAN_REMOVE_ENTITY => 'removeReferencesToEntity',
        );
    }
}
