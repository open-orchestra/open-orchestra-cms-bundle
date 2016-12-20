<?php

namespace OpenOrchestra\Backoffice\Manager;

use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Saver\VersionableSaverInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OpenOrchestra\ModelInterface\Repository\BlockRepositoryInterface;

/**
 * Class NodeManager
 */
class NodeManager
{
    protected $versionableSaver;
    protected $statusRepository;
    protected $blockRepository;
    protected $eventDispatcher;
    protected $nodeRepository;
    protected $siteRepository;
    protected $contextManager;
    protected $nodeClass;

    /**
     * Constructor
     *
     * @param VersionableSaverInterface  $versionableSaver
     * @param NodeRepositoryInterface    $nodeRepository
     * @param SiteRepositoryInterface    $siteRepository
     * @param StatusRepositoryInterface  $statusRepository
     * @param BlockRepositoryInterface   $blockRepository
     * @param ContextManager             $contextManager
     * @param string                     $nodeClass
     * @param EventDispatcherInterface   $eventDispatcher
     */
    public function __construct(
        VersionableSaverInterface  $versionableSaver,
        NodeRepositoryInterface $nodeRepository,
        SiteRepositoryInterface $siteRepository,
        StatusRepositoryInterface $statusRepository,
        BlockRepositoryInterface  $blockRepository,
        ContextManager $contextManager,
        $nodeClass,
        $eventDispatcher
    ){
        $this->versionableSaver =  $versionableSaver;
        $this->nodeRepository = $nodeRepository;
        $this->siteRepository = $siteRepository;
        $this->statusRepository = $statusRepository;
        $this->blockRepository = $blockRepository;
        $this->contextManager = $contextManager;
        $this->nodeClass = $nodeClass;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Duplicate a node
     *
     * @param string    $nodeId
     * @param string    $siteId
     * @param string    $language
     * @param int|null  $version
     * @param bool|true $save
     *
     * @return NodeInterface
     */
    public function duplicateNode($nodeId, $siteId, $language, $version = null, $save = true)
    {
        $node = $this->nodeRepository->findVersion($nodeId, $language, $siteId, $version);
        $lastNode = $this->nodeRepository->findInLastVersion($nodeId, $language, $siteId);
        $lastNodeVersion = $lastNode->getVersion();
        $status = $this->statusRepository->findOneByInitial();

        /** @var NodeInterface $newNode */
        $newNode = clone $node;
        $newNode->setStatus($status);
        $newNode->setCurrentlyPublished($status->isPublishedState());
        $newNode->setVersion($lastNodeVersion + 1);
        $this->duplicateBlockAndArea($node, $newNode);

        if (true === $save) {
            $this->saveDuplicatedNode($newNode);
        }

        return $newNode;
    }

    /**
     * @param NodeInterface $newNode
     */
    public function saveDuplicatedNode(NodeInterface $newNode)
    {
        $this->versionableSaver->saveDuplicated($newNode);
    }

    /**
     * @param string $nodeId
     * @param string $siteId
     * @param string $language
     *
     * @return NodeInterface
     */
    public function createNewErrorNode($nodeId, $siteId, $language)
    {
        $node = $this->initializeNode(NodeInterface::ROOT_NODE_ID, $language, $siteId);
        $node->setNodeId($nodeId);
        $node->setNodeType(ReadNodeInterface::TYPE_ERROR);
        $node->setRoutePattern($nodeId);
        $node->setName($nodeId);
        $node->setInFooter(false);
        $node->setInMenu(false);
        $node->setVersion(1);

        $this->eventDispatcher->dispatch(NodeEvents::NODE_CREATION, new NodeEvent($node));

        return $node;
    }

    /**
     * @param NodeInterface $node
     * @param string        $language
     *
     * @return NodeInterface
     */
    public function createNewLanguageNode(NodeInterface $node, $language)
    {
        $newNode = clone $node;
        $newNode->setVersion(1);
        $status = $this->statusRepository->findOneByTranslationState();

        $newNode->setStatus($status);
        $newNode = $this->duplicateBlockAndArea($node, $newNode);

        $newNode->setLanguage($language);

        $this->eventDispatcher->dispatch(NodeEvents::NODE_ADD_LANGUAGE, new NodeEvent($node));

        return $newNode;
    }

    /**
     * @param mixed $nodes
     */
    public function deleteTree($nodes)
    {
        $siteId = $this->contextManager->getCurrentSiteId();
        foreach ($nodes as $node) {
            if (!$node->isDeleted()) {
                $node->setDeleted(true);
                $node->setOrder(NodeInterface::DELETED_ORDER);
                $nodePath = $node->getPath();
                $this->eventDispatcher->dispatch(NodeEvents::NODE_DELETE, new NodeEvent($node));
                $subNodes = $this->nodeRepository->findByIncludedPathAndSiteId($nodePath, $siteId);
                foreach ($subNodes as $subNode) {
                    if (!$subNode->isDeleted()) {
                        $subNode->setDeleted(true);
                        $subNode->setOrder(NodeInterface::DELETED_ORDER);
                        $this->eventDispatcher->dispatch(NodeEvents::NODE_DELETE, new NodeEvent($subNode));
                    }
                }
            }
        }
    }

    /**
     * @param NodeInterface $node
     * @param string        $nodeId
     *
     * @return NodeInterface
     */
    public function hydrateNodeFromNodeId(NodeInterface $node, $nodeId)
    {
        $siteId = $this->contextManager->getCurrentSiteId();
        $oldNode = $this->nodeRepository->findInLastVersion($nodeId, $node->getLanguage(), $siteId);
        dump($oldNode);
        dump($node->getLanguage());
        dump($siteId);
        if ($oldNode) {
            $node->setTemplate($oldNode->getTemplate());
            $this->duplicateBlockAndArea($oldNode, $node);
        }
        dump($node);

        return $node;
    }

    /**
     * @param NodeInterface $node
     * @param NodeInterface $newNode
     *
     * @return NodeInterface
     */
    protected function duplicateBlockAndArea(NodeInterface $node, NodeInterface $newNode)
    {
        foreach ($node->getAreas() as $areaId => $area) {
            $newArea = clone $area;
            $newNode->setArea($areaId, $newArea);
            foreach ($area->getBlocks() as $block) {
                if (!$block->isTransverse()) {
                    $newBlock = clone $block;
                    $this->blockRepository->getDocumentManager()->persist($newBlock);
                    $newArea->addBlock($newBlock);
                }
            }
        }

        return $newNode;
    }

    /**
     * @param string $siteId
     * @param string $language
     * @param string $name
     * @param string $routePattern
     * @param string $template
     *
     * @return NodeInterface
     */
    public function createRootNode($siteId, $language, $name, $routePattern, $template)
    {
        $node = $this->initializeNode(NodeInterface::ROOT_PARENT_ID, $language, $siteId);
        $node->setTemplate($template);
        $node->setRoutePattern($routePattern);
        $node->setName($name);
        $node->setVersion(1);
        $node->setInMenu(true);
        $node->setInFooter(true);

        return $node;
    }

    /**
     * @param string $parentId
     * @param string $language
     * @param string $siteId
     * @param int    $order
     *
     * @return NodeInterface
     */
    public function initializeNode($parentId, $language, $siteId, $order = 0)
    {
        /** @var NodeInterface $node */
        $node = new $this->nodeClass();
        $node->setSiteId($siteId);
        $node->setLanguage($language);
        $node->setMaxAge(NodeInterface::MAX_AGE);
        $node->setParentId($parentId);
        $node->setOrder($order);
        $node->setTheme(NodeInterface::THEME_DEFAULT);
        $node->setDefaultSiteTheme(true);

        $parentNode = $this->nodeRepository->findVersion($parentId, $language, $siteId);
        $status = $this->statusRepository->findOneByInitial();
        $node->setStatus($status);
        $nodeType = NodeInterface::TYPE_DEFAULT;
        if ($parentNode instanceof NodeInterface) {
            $nodeType = $parentNode->getNodeType();
        } else {
            $node->setNodeId(NodeInterface::ROOT_NODE_ID);
        }
        $node->setNodeType($nodeType);

        return $node;
    }

   /**
     * @param array         $orderedNode
     * @param NodeInterface $node
     */
    public function orderNodeChildren($orderedNode, NodeInterface $node)
    {
        $nodeId = $node->getNodeId();
        foreach ($orderedNode as $position => $childNodeId) {
            $siteId = $this->contextManager->getCurrentSiteId();
            $children = $this->nodeRepository->findByNodeAndSite($childNodeId, $siteId);
            $path = $node->getPath() . '/' . $childNodeId;
            /** @var NodeInterface $child */
            foreach ($children as $child) {
                $child->setOrder($position);
                $child->setParentId($nodeId);
                $child->setPath($path);
            }
            $event = new NodeEvent($child);
            $this->eventDispatcher->dispatch(NodeEvents::PATH_UPDATED, $event);
        }
    }
}
