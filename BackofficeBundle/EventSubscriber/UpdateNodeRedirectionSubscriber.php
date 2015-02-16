<?php

namespace PHPOrchestra\BackofficeBundle\EventSubscriber;

use PHPOrchestra\BackofficeBundle\Manager\RedirectionManager;
use PHPOrchestra\ModelInterface\Event\NodeEvent;
use PHPOrchestra\ModelInterface\NodeEvents;
use PHPOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateNodeRedirectionSubscriber
 */
class UpdateNodeRedirectionSubscriber implements EventSubscriberInterface
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
        if ($node->getStatus()->isPublished()) {
            $nodes = $this->nodeRepository->findByNodeIdAndLanguageAndSiteIdAndPublishedOrderedByVersion($node->getNodeId(), $node->getLanguage());
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

        $parent = $this->nodeRepository->findOneByNodeIdAndLanguageWithPublishedAndLastVersionAndSiteId($parentId, $language);

        return str_replace('//', '/', $this->completeRoutePattern($parent->getParentId(), $parent->getRoutePattern() . '/' . $suffix, $language));
    }
}
