<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\StatusableEvent;
use OpenOrchestra\ModelInterface\StatusEvents;
use OpenOrchestra\BackofficeBundle\StrategyManager\ExtractReferenceManager;
use OpenOrchestra\Media\Model\MediaInterface;
use OpenOrchestra\Media\Repository\MediaRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateMediaReferenceSubscriber
 */
class UpdateMediaReferenceSubscriber implements EventSubscriberInterface
{
    protected $extractReferenceManager;
    protected $mediaRepository;

    /**
     * @param ExtractReferenceManager  $extractReferenceManager
     * @param MediaRepositoryInterface $mediaRepository
     */
    public function __construct(ExtractReferenceManager $extractReferenceManager, MediaRepositoryInterface $mediaRepository)
    {
        $this->extractReferenceManager = $extractReferenceManager;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * @param StatusableEvent $event
     */
    public function updateMediaReference(StatusableEvent $event)
    {
        $statusableElement = $event->getStatusableElement();
        $references = $this->extractReferenceManager->extractReference($statusableElement);

        $methodToCall = 'removeUsageReference';
        if ($statusableElement->getStatus()->isPublished()) {
            $methodToCall = 'addUsageReference';
        }

        foreach ($references as $mediaId => $mediaUsage) {
            /** @var MediaInterface $media */
            $media = $this->mediaRepository->find($mediaId);
            foreach ($mediaUsage as $usage) {
                $media->$methodToCall($usage);
            }
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            StatusEvents::STATUS_CHANGE => 'updateMediaReference',
        );
    }
}
