<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\BackofficeBundle\Manager\RedirectionManager;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Event\SiteEvent;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\SiteEvents;
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
        $previousStatus = $event->getPreviousStatus();
        if ($node->getStatus()->isPublished() || (!$node->getStatus()->isPublished() && $previousStatus->isPublished())) {
            $this->generateRouteForNode($node);
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
    public function updateRedirectionOnSiteUpdate(SiteEvent $event)
    {
        $siteId = $event->getSite()->getCurrentSiteId();
        $nodes = $this->nodeRepository->findLastVersionBySiteId($siteId);
        foreach ($nodes as $node) {
            $siteId = $this->currentSiteManager->getCurrentSiteId();
            $this->generateRouteForNode($node);
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            NodeEvents::NODE_CHANGE_STATUS => 'updateRedirection',
            NodeEvents::NODE_RESTORE => 'updateRedirectionRoutes',
            SiteEvents::SITE_UPDATE => 'updateRedirectionOnSiteUpdate',
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
        $parent = $this->nodeRepository->findPublishedInLastVersion($parentId, $language, $siteId);

        if ($parent instanceof NodeInterface) {
            return str_replace('//', '/', $this->completeRoutePattern($parent->getParentId(), $parent->getRoutePattern() . '/' . $suffix, $language));
        }

        return $suffix;
    }

    /**
     * @param NodeInterface $node
     */

    protected function generateRouteForNode(NodeInterface $node)
    {
        $siteId = $node->getSiteId();
        $nodes = $this->nodeRepository->findPublishedSortedByVersion($node->getNodeId(), $node->getLanguage(), $siteId);
        $this->redirectionManager->deleteRedirection(
            $node->getNodeId(),
            $node->getLanguage()
            );
        if (count($nodes) > 0) {
            $lastNode = array_shift($nodes);
            $routePatterns = array($this->completeRoutePattern($lastNode->getParentId(), $node->getRoutePattern(), $node->getLanguage()));

            foreach ($nodes as $otherNode) {
                $oldRoutePattern = $this->completeRoutePattern($otherNode->getParentId(), $otherNode->getRoutePattern(), $otherNode->getLanguage());
                if (!in_array($oldRoutePattern, $routePatterns)) {
                    $this->redirectionManager->createRedirection(
                        $oldRoutePattern,
                        $node->getNodeId(),
                        $node->getLanguage()
                        );
                    array_push($routePatterns, $oldRoutePattern);
                }
            }
        }
    }
}
