<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\Backoffice\Manager\RedirectionManager;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
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
        if ($node->getStatus()->isPublishedState() || (!$node->getStatus()->isPublishedState() && $previousStatus->isPublishedState())) {
            $this->redirectionManager->generateRedirectionForNode($node);
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            NodeEvents::NODE_CHANGE_STATUS => 'updateRedirection'
        );
    }
}
