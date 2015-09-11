<?php

namespace OpenOrchestra\Backoffice\RestoreEntity\Strategies;

use OpenOrchestra\Backoffice\RestoreEntity\RestoreEntityInterface;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class RestoreContentStrategy
 */
class RestoreContentStrategy implements RestoreEntityInterface
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
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->contentRepository = $contentRepository;

    }

    /**
     * @param mixed $entity
     *
     * @return bool
     */
    public function support($entity)
    {
        return $entity instanceof ContentInterface;
    }

    /**
     * @param ContentInterface $content
     */
    public function restore($content)
    {
        $contents = $this->contentRepository->findByContentId($content->getContentId());
        /** @var ContentInterface $content */
        foreach ($contents as $content) {
            $content->setDeleted(false);
        }
        $this->eventDispatcher->dispatch(ContentEvents::CONTENT_RESTORE, new ContentEvent($content));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'restore_content';
    }
}
