<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\BackofficeBundle\Manager\RedirectionManager;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateNodeRedirectionSubscriber
 */
class UpdateNodeRedirectionSubscriber implements EventSubscriberInterface
{
    protected $nodeRepository;
    protected $redirectionManager;
    protected $currentSiteManager;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     * @param RedirectionManager      $redirectionManager
     * @param CurrentSiteIdInterface  $currentSiteManager
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, RedirectionManager $redirectionManager, CurrentSiteIdInterface $currentSiteManager)
    {
        $this->nodeRepository = $nodeRepository;
        $this->redirectionManager = $redirectionManager;
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * @param NodeEvent $event
     */
    public function updateRedirection(NodeEvent $event)
    {
        $node = $event->getNode();
        if ($node->getStatus()->isPublished()) {
            $siteId = $this->currentSiteManager->getCurrentSiteId();
            $nodes = $this->nodeRepository->findByNodeIdAndLanguageAndSiteIdAndPublishedOrderedByVersion($node->getNodeId(), $node->getLanguage(), $siteId);
            foreach ($nodes as $otherNode) {
                if ($otherNode->getId() != $node->getId() && $otherNode->getRoutePattern() != $node->getRoutePattern()) {
                    $this->redirectionManager->createRedirection(
                        $this->completeRoutePattern($node->getParentId(), $otherNode->getRoutePattern(), $node->getLanguage()),
                        $node->getNodeId(),
                        $node->getLanguage()
                    );
                }
            }
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            NodeEvents::NODE_CHANGE_STATUS => 'updateRedirection',
        );
    }

    /**
     * @param string|null $parentId
     * @param string|null $suffix
     *
     * @return string|null
     */
    protected function completeRoutePattern($parentId = null, $suffix = null, $language)
    {
        if (is_null($parentId) || '-' == $parentId || '' == $parentId) {
            return $suffix;
        }
        $siteId = $this->currentSiteManager->getCurrentSiteId();
        $parent = $this->nodeRepository->findOnePublishedByNodeIdAndLanguageAndSiteIdInLastVersion($parentId, $language, $siteId);

        if ($parent instanceof NodeInterface) {
            return str_replace('//', '/', $this->completeRoutePattern($parent->getParentId(), $parent->getRoutePattern() . '/' . $suffix, $language));
        }

        return $suffix;
    }
}
