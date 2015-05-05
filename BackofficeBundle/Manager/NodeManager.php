<?php

namespace OpenOrchestra\BackofficeBundle\Manager;

use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\Backoffice\Context\ContextManager;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class NodeManager
 */
class NodeManager
{
    protected $eventDispatcher;
    protected $nodeRepository;
    protected $siteRepository;
    protected $statusRepository;
    protected $contextManager;
    protected $blockManager;
    protected $areaManager;
    protected $nodeClass;

    /**
     * Constructor
     *
     * @param NodeRepositoryInterface    $nodeRepository
     * @param SiteRepositoryInterface    $siteRepository
     * @param StatusRepositoryInterface  $statusRepository
     * @param AreaManager                $areaManager
     * @param BlockManager               $blockManager
     * @param ContextManager             $contextManager
     * @param string                     $nodeClass
     * @param EventDispatcherInterface   $eventDispatcher
     */
    public function __construct(
        NodeRepositoryInterface $nodeRepository,
        SiteRepositoryInterface $siteRepository,
        StatusRepositoryInterface $statusRepository,
        AreaManager $areaManager,
        BlockManager $blockManager,
        ContextManager $contextManager,
        $nodeClass,
        $eventDispatcher
    )
    {
        $this->nodeRepository = $nodeRepository;
        $this->siteRepository = $siteRepository;
        $this->statusRepository = $statusRepository;
        $this->areaManager = $areaManager;
        $this->blockManager = $blockManager;
        $this->contextManager = $contextManager;
        $this->nodeClass = $nodeClass;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Duplicate a node
     *
     * @param NodeInterface $node
     *
     * @return NodeInterface
     */
    public function duplicateNode(NodeInterface $node)
    {
        $newNode = clone $node;
        $newNode->setVersion($node->getVersion() + 1);
        $newNode->setStatus($this->getInitialStatus($node));
        $newNode = $this->duplicateBlockAndArea($node, $newNode);

        $this->eventDispatcher->dispatch(NodeEvents::NODE_DUPLICATE, new NodeEvent($node));

        return $newNode;
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
        $newNode->setStatus($this->getInitialStatus($node));
        $newNode->setLanguage($language);
        $newNode = $this->duplicateBlockAndArea($node, $newNode);

        $this->eventDispatcher->dispatch(NodeEvents::NODE_ADD_LANGUAGE, new NodeEvent($node));

        return $newNode;
    }

    /**
     * @param NodeInterface $node
     *
     * @return StatusInterface
     */
    protected function getInitialStatus(NodeInterface $node)
    {
        if ($node->getNodeId() == NodeInterface::TRANSVERSE_NODE_ID) {
            return $this->statusRepository->findOneByEditable();
        }

        return null;
    }

    /**
     * @param mixed $nodes
     */
    public function deleteTree($nodes)
    {
        $parentId = null;
        $node = null;
        foreach ($nodes as $node) {
            $node->setDeleted(true);
            $parentId = $node->getNodeId();
        }

        if ($parentId) {
            $sons = $this->nodeRepository->findByParentIdAndSiteId($parentId);
            $this->deleteTree($sons);
        }

        if ($node) {
            $this->eventDispatcher->dispatch(NodeEvents::NODE_DELETE, new NodeEvent($node));
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
        $oldNode = $this->nodeRepository->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion($nodeId, $node->getLanguage());

        if ($oldNode) {
            $this->duplicateBlockAndArea($oldNode, $node);
        }

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
        foreach ($node->getAreas() as $area) {
            $newArea = clone $area;
            $newNode->addArea($newArea);
        }
        foreach ($node->getBlocks() as $block) {
            $newBlock = clone $block;
            $newNode->addBlock($newBlock);
        }

        return $newNode;
    }

    /**
     * @param array $nodes
     *
     * @return bool
     */
    public function nodeConsistency($nodes)
    {
        if (is_array($nodes)) {
            foreach ($nodes as $node) {
                if (!$this->areaManager->areaConsistency($node) || !$this->blockManager->blockConsistency($node)) {
                    return false;
                }
            }

            return true;
        }
        return false;
    }

    /**
     * @param string $parentId
     *
     * @return NodeInterface
     */
    public function initializeNewNode($parentId)
    {
        $node = new $this->nodeClass();
        $node->setSiteId($this->contextManager->getCurrentSiteId());
        $node->setLanguage($this->contextManager->getCurrentSiteDefaultLanguage());
        $node->setMaxAge(NodeInterface::MAX_AGE);
        $node->setParentId($parentId);
        $parentNode = $this->nodeRepository->findOneByNodeIdAndLanguageAndVersionAndSiteId($parentId);
        $node->setStatus($this->getInitialStatus($parentNode));
        $node->setNodeType($parentNode->getNodeType());

        $site = $this->siteRepository->findOneBySiteId($this->contextManager->getCurrentSiteId());
        if ($site) {
            $node->setMetaKeywords($site->getMetaKeywords());
            $node->setMetaDescription($site->getMetaDescription());
            $node->setMetaIndex($site->getMetaIndex());
            $node->setMetaFollow($site->getMetaFollow());
            if ($theme = $site->getTheme()) {
                $node->setTheme($theme->getName());
            }
        }

        return $node;
    }

    /**
     * @param NodeInterface $oldNode
     * @param NodeInterface $node
     */
    public function updateBlockReferences(NodeInterface $oldNode, NodeInterface $node)
    {
        $nodeTransverse = $this->nodeRepository
            ->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion(NodeInterface::TRANSVERSE_NODE_ID, $node->getLanguage(), $node->getSiteId());

        foreach($node->getAreas() as $area) {
            foreach ($area->getBlocks() as $areaBlock) {
                if (NodeInterface::TRANSVERSE_NODE_ID === $areaBlock['nodeId']) {
                    $block = $nodeTransverse->getBlock($areaBlock['blockId']);
                    $block->addArea(array('nodeId' => $node->getId(), 'areaId' => $area->getAreaId()));
                    continue;
                }
                $block = $node->getBlock($areaBlock['blockId']);
                foreach ($block->getAreas() as $blockArea) {
                    if ($blockArea['nodeId'] === $oldNode->getId()) {
                        $blockArea['nodeId'] = $node->getId();
                    }
                }
            }
        }
    }

    /**
     * @param array         $orderedNode
     * @param NodeInterface $node
     */
    public function orderNodeChildren($orderedNode, NodeInterface $node)
    {
        $nodeId = $node->getNodeId();
        foreach ($orderedNode as $position => $childNodeId) {
            $childs = $this->nodeRepository->findByNodeIdAndSiteId($childNodeId);
            $path = $node->getPath() . '/' . $childNodeId;
            /** @var NodeInterface $child */
            foreach ($childs as $child) {
                $child->setOrder($position);
                $child->setParentId($nodeId);
                $child->setPath($path);
            }
            $event = new NodeEvent($child);
            $this->eventDispatcher->dispatch(NodeEvents::PATH_UPDATED, $event);
        }
    }
}
