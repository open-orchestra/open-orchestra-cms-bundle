<?php

namespace OpenOrchestra\Backoffice\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\Event\EventTrait\EventStatusableInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
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
        $node = $event->getStatusableElement();

        if ($node->getStatus()->isPublished()) {
            $lastPublishedNode = $this->repository->findOneCurrentlyPublished($node->getElementId(), $node->getLanguage(), $node->getSiteId());
            if (!($lastPublishedNode instanceof StatusableInterface) || $lastPublishedNode->getVersion() <= $node->getVersion()) {
                $this->updatePublishedFlag($node);
            }
        } elseif ($node->isCurrentlyPublished()) {
            $lastPublishedNode = $this->repository->findPublishedInLastVersionWithoutFlag($node->getElementId(), $node->getLanguage(), $node->getSiteId());
            if ($lastPublishedNode instanceof StatusableInterface && $lastPublishedNode->getVersion() < $node->getVersion()) {
                $this->updatePublishedFlag($lastPublishedNode);
            } else {
                $node->setCurrentlyPublished(false);
                $this->objectManager->flush($node);
            }
        }
    }

    /**
     * @param StatusableInterface $node
     */
    protected function updatePublishedFlag(StatusableInterface $node)
    {
        $publishedNodes = $this->repository->findAllCurrentlyPublishedByElementId($node->getElementId(), $node->getLanguage(), $node->getSiteId());
        /** @var NodeInterface $publishedNode */
        foreach ($publishedNodes as $publishedNode) {
            $publishedNode->setCurrentlyPublished(false);
            $this->objectManager->flush($publishedNode);
        }

        $node->setCurrentlyPublished(true);
        $this->objectManager->flush($node);
    }
}
