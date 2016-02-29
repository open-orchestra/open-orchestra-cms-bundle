<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\Backoffice\Manager\NodeManager;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateNodeCurrentlyPublishedFlagSubscriber
 */
class UpdateNodeCurrentlyPublishedFlagSubscriber implements EventSubscriberInterface
{
    protected $nodeRepository;
    protected $objectManager;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     * @param ObjectManager           $objectManager
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, ObjectManager $objectManager)
    {
        $this->nodeRepository = $nodeRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @param NodeEvent $event
     */
    public function updateFlag(NodeEvent $event)
    {
        $node = $event->getNode();

        if ($node->getStatus()->isPublished()) {
            $lastPublishedNode = $this->nodeRepository->findOneCurrentlyPublished($node->getNodeId(), $node->getLanguage(), $node->getSiteId());
            if (!($lastPublishedNode instanceof NodeInterface) || $lastPublishedNode->getVersion() <= $node->getVersion()) {
                $this->updatePublishedFlag($node);
            }
        } elseif ($node->isCurrentlyPublished()) {
            $lastPublishedNode = $this->nodeRepository->findPublishedInLastVersionWithoutFlag($node->getNodeId(), $node->getLanguage(), $node->getSiteId());
            if ($lastPublishedNode instanceof NodeInterface && $lastPublishedNode->getVersion() < $node->getVersion()) {
                $this->updatePublishedFlag($lastPublishedNode);
            } else {
                $node->setCurrentlyPublished(false);
                $this->objectManager->flush($node);
            }
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            NodeEvents::NODE_CHANGE_STATUS => array('updateFlag', 100),
        );
    }

    /**
     * @param NodeInterface $node
     */
    protected function updatePublishedFlag(NodeInterface $node)
    {
        $publishedNodes = $this->nodeRepository->findAllCurrentlyPublishedByNode($node->getNodeId(), $node->getLanguage(), $node->getSiteId());
        /** @var NodeInterface $publishedNode */
        foreach ($publishedNodes as $publishedNode) {
            $publishedNode->setCurrentlyPublished(false);
            $this->objectManager->flush($publishedNode);
        }

        $node->setCurrentlyPublished(true);
        $this->objectManager->flush($node);
    }
}
