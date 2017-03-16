<?php

namespace OpenOrchestra\Backoffice\TrashcanEntity\Strategies;

use OpenOrchestra\Backoffice\TrashcanEntity\TrashCanEntityInterface;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Event\TrashcanEvent;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use OpenOrchestra\ModelInterface\TrashcanEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class TrashCanContentStrategy
 */
class TrashCanContentStrategy implements TrashCanEntityInterface
{
    protected $eventDispatcher;
    protected $contentRepository;

    /**
     * @param ContentRepositoryInterface $contentRepository
     * @param EventDispatcherInterface   $eventDispatcher
     */
    public function __construct(
        ContentRepositoryInterface $contentRepository,
        EventDispatcherInterface $eventDispatcher
    ){
        $this->eventDispatcher = $eventDispatcher;
        $this->contentRepository = $contentRepository;
    }

    /**
     * @param TrashItemInterface $trashItem
     *
     * @return bool
     */
    public function support(TrashItemInterface $trashItem)
    {
        return ContentInterface::TRASH_ITEM_TYPE === $trashItem->getType();
    }

    /**
     * @param TrashItemInterface $trashItem
     */
    public function remove(TrashItemInterface $trashItem)
    {
        $storageIds = array();
        $contents = $this->contentRepository->findByContentId($trashItem->getEntityId());
        foreach ($contents as $content) {
                $storageIds[] = $content->getId();
                $this->eventDispatcher->dispatch(TrashcanEvents::TRASHCAN_REMOVE_ENTITY, new TrashcanEvent($content));
        }
        $this->contentRepository->removeContentVersion($storageIds);
    }

    /**
     * @param TrashItemInterface $trashItem
     */
    public function restore(TrashItemInterface $trashItem)
    {
        $contents = $this->contentRepository->findByContentId($trashItem->getEntityId());
        foreach ($contents as $content) {
            $this->eventDispatcher->dispatch(ContentEvents::CONTENT_RESTORE, new ContentEvent($content));
        }
        $this->contentRepository->restoreDeletedContent($trashItem->getEntityId());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'remove_trashcan_content';
    }
}
