<?php

namespace OpenOrchestra\Backoffice\Manager;

use Doctrine\ODM\MongoDB\DocumentManager;
use OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface;
use OpenOrchestra\ModelInterface\Event\RedirectionEvent;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\SiteAliasInterface;
use OpenOrchestra\ModelInterface\RedirectionEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\RedirectionRepositoryInterface;

/**
 * Class RedirectionManager
 */
class RedirectionManager
{
    protected $redirectionClass;
    protected $documentManager;
    protected $eventDispatcher;
    protected $contextManager;
    protected $siteRepository;
    protected $nodeRepository;
    protected $redirectionRepository;

    /**
     * @param string                         $redirectionClass
     * @param ContextBackOfficeInterface     $contextManager
     * @param DocumentManager                $documentManager
     * @param EventDispatcherInterface       $eventDispatcher
     * @param SiteRepositoryInterface        $siteRepository
     * @param NodeRepositoryInterface        $nodeRepository
     * @param RedirectionRepositoryInterface $siteRepository
     */
    public function __construct(
        $redirectionClass,
        ContextBackOfficeInterface $contextManager,
        DocumentManager $documentManager,
        EventDispatcherInterface $eventDispatcher,
        SiteRepositoryInterface $siteRepository,
        NodeRepositoryInterface $nodeRepository,
        RedirectionRepositoryInterface $redirectionRepository
    ){
        $this->redirectionClass = $redirectionClass;
        $this->contextManager = $contextManager;
        $this->documentManager = $documentManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->siteRepository = $siteRepository;
        $this->nodeRepository = $nodeRepository;
        $this->redirectionRepository = $redirectionRepository;
    }

    /**
     * @param string $pattern
     * @param string $nodeId
     * @param string $language
     */
    public function createRedirection($pattern, $nodeId, $language)
    {
        $redirectionClass = $this->redirectionClass;
        $site = $this->siteRepository->findOneBySiteId($this->contextManager->getSiteId());
        /** @var SiteAliasInterface $alias */
        foreach ($site->getAliases() as $alias) {
            if ($language == $alias->getLanguage()) {
                /** @var RedirectionInterface $redirection */
                $redirection = new $redirectionClass();
                $redirection->setNodeId($nodeId);
                $redirection->setLocale($language);
                $redirection->setRoutePattern(preg_replace('#///|//#', '/', '/' . $alias->getPrefix() . '/' . $pattern));
                $redirection->setSiteId($site->getSiteId());
                $redirection->setSiteName($site->getName());
                $this->documentManager->persist($redirection);
                $this->documentManager->flush($redirection);
                $this->eventDispatcher->dispatch(RedirectionEvents::REDIRECTION_CREATE, new RedirectionEvent($redirection));
            }
        }
    }

    /**
     * @param string $nodeId
     * @param string $language
     * @param string $siteId
     */
    public function deleteRedirection($nodeId, $language, $siteId)
    {
        $redirections = $this->redirectionRepository->findByNode($nodeId, $language, $siteId);
        foreach ($redirections as $redirection) {
            $this->documentManager->remove($redirection);
            $this->documentManager->flush($redirection);
            $this->eventDispatcher->dispatch(RedirectionEvents::REDIRECTION_DELETE, new RedirectionEvent($redirection));
        }
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
        $siteId = $this->contextManager->getSiteId();
        $parent = $this->nodeRepository->findOnePublished($parentId, $language, $siteId);

        if ($parent instanceof NodeInterface) {
            return str_replace('//', '/', $this->completeRoutePattern($parent->getParentId(), $parent->getRoutePattern() . '/' . $suffix, $language));
        }

        return $suffix;
    }

    /**
     * @param NodeInterface $node
     */
    public function generateRedirectionForNode(NodeInterface $node)
    {
        $siteId = $node->getSiteId();

        $this->deleteRedirection(
            $node->getNodeId(),
            $node->getLanguage(),
            $siteId
            );
        $nodes = $this->nodeRepository->findPublishedSortedByVersion($node->getNodeId(), $node->getLanguage(), $siteId);
        if (count($nodes) > 0) {
            $lastNode = array_shift($nodes);
            $routePatterns = array($this->completeRoutePattern($lastNode->getParentId(), $node->getRoutePattern(), $node->getLanguage()));

            foreach ($nodes as $otherNode) {
                $oldRoutePattern = $this->completeRoutePattern($otherNode->getParentId(), $otherNode->getRoutePattern(), $otherNode->getLanguage());
                if (!in_array($oldRoutePattern, $routePatterns)) {
                    $this->createRedirection(
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
