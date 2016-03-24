<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\Backoffice\Manager\RedirectionManager;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateRedirectionNodeSubscriber
 */
class UpdateRedirectionNodeSubscriber implements EventSubscriberInterface
{
    protected $nodeRepository;
    protected $redirectionManager;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     * @param RedirectionManager      $redirectionManager
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, RedirectionManager $redirectionManager)
    {
        $this->nodeRepository = $nodeRepository;
        $this->redirectionManager = $redirectionManager;
    }

    /**
     * @param NodeEvent $event
     */
    public function updateRedirection(NodeEvent $event)
    {
        $node = $event->getNode();
        $previousStatus = $event->getPreviousStatus();
        if ($node->getStatus()->isPublished() || (!$node->getStatus()->isPublished() && $previousStatus->isPublished())) {
            $this->redirectionManager->generateRedirectionForNode($node);
        }
    }

    /**
     * @param NodeEvent $event
     */
    public function updateRedirectionRoutes(NodeEvent $event)
    {
        $node = $event->getNode();
        $this->redirectionManager->updateRedirection(
            $node->getNodeId(),
            $node->getLanguage()
        );
    }

    /**
     * @param NodeEvent $event
     */
    public function updateRedirectionRoutesOnNodeDelete(NodeEvent $event)
    {
        $node = $event->getNode();
        $this->deleteRedirectionForNodeTree($node);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            NodeEvents::NODE_CHANGE_STATUS => 'updateRedirection',
            NodeEvents::NODE_RESTORE => 'updateRedirectionRoutes',
            NodeEvents::NODE_DELETE => 'updateRedirectionRoutesOnNodeDelete',
        );
    }

    /**
     * @param NodeInterface $node
     */
    protected function deleteRedirectionForNodeTree(NodeInterface $node)
    {
        $this->redirectionManager->deleteRedirection(
            $node->getNodeId(),
            $node->getLanguage()
        );

        $nodes = $this->nodeRepository->findByParent($node->getNodeId(), $node->getSiteId());
        foreach ($nodes as $node) {
            $this->deleteRedirectionForNodeTree($node);
        }
    }
}
