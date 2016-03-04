<?php

namespace OpenOrchestra\Backoffice\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\Event\EventTrait\EventStatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Repository\StatusableRepositoryInterface;

/**
 * Class UpdateStatusableElementCurrentlyPublishedFlagListener
 */
class UpdateStatusableElementCurrentlyPublishedFlagListener
{
    protected $repository;
    protected $objectManager;

    /**
     * @param StatusableRepositoryInterface $repository
     * @param ObjectManager                 $objectManager
     */
    public function __construct(StatusableRepositoryInterface $repository, ObjectManager $objectManager)
    {
        $this->repository = $repository;
        $this->objectManager = $objectManager;
    }

    /**
     * @param EventStatusableInterface $event
     */
    public function updateFlag(EventStatusableInterface $event)
    {
        $statusableElement = $event->getStatusableElement();

        if ($statusableElement->getStatus()->isPublished()) {
            $lastPublishedNode = $this->repository->findOneCurrentlyPublishedByElement($statusableElement);
            if (!($lastPublishedNode instanceof StatusableInterface) || $lastPublishedNode->getVersion() <= $statusableElement->getVersion()) {
                $this->updatePublishedFlag($statusableElement);
            }
        } elseif ($statusableElement->isCurrentlyPublished()) {
            $lastPublishedNode = $this->repository->findPublishedInLastVersionWithoutFlag($statusableElement);
            if ($lastPublishedNode instanceof StatusableInterface && $lastPublishedNode->getVersion() < $statusableElement->getVersion()) {
                $this->updatePublishedFlag($lastPublishedNode);
            } else {
                $statusableElement->setCurrentlyPublished(false);
                $this->objectManager->flush($statusableElement);
            }
        }
    }

    /**
     * @param StatusableInterface $statusableElement
     */
    protected function updatePublishedFlag(StatusableInterface $statusableElement)
    {
        $publishedElements = $this->repository->findAllCurrentlyPublishedByElementId($statusableElement);
        /** @var StatusableInterface $publishedNode */
        foreach ($publishedElements as $publishedElement) {
            $publishedElement->setCurrentlyPublished(false);
            $this->objectManager->flush($publishedElement);
        }

        $statusableElement->setCurrentlyPublished(true);
        $this->objectManager->flush($statusableElement);
    }
}
