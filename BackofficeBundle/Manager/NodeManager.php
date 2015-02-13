<?php

namespace PHPOrchestra\BackofficeBundle\Manager;

use PHPOrchestra\ModelInterface\Event\NodeEvent;
use PHPOrchestra\ModelInterface\NodeEvents;
use PHPOrchestra\ModelInterface\Model\NodeInterface;
use PHPOrchestra\Backoffice\Context\ContextManager;
use PHPOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use PHPOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class NodeManager
 */
class NodeManager
{
    protected $eventDispatcher;
    protected $nodeRepository;
    protected $siteRepository;
    protected $contextManager;
    protected $blockManager;
    protected $areaManager;
    protected $nodeClass;

    /**
     * Constructor
     *
     * @param NodeRepositoryInterface  $nodeRepository
     * @param SiteRepositoryInterface  $siteRepository
     * @param AreaManager              $areaManager
     * @param BlockManager             $blockManager
     * @param ContextManager           $contextManager
     * @param string                   $nodeClass
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        NodeRepositoryInterface $nodeRepository,
        SiteRepositoryInterface $siteRepository,
        AreaManager $areaManager,
        BlockManager $blockManager,
        ContextManager $contextManager,
        $nodeClass,
        $eventDispatcher
    )
    {
        $this->nodeRepository = $nodeRepository;
        $this->siteRepository = $siteRepository;
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
        $newNode->setStatus(null);
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
        $newNode->setStatus(null);
        $newNode->setLanguage($language);
        $newNode = $this->duplicateBlockAndArea($node, $newNode);

        $this->eventDispatcher->dispatch(NodeEvents::NODE_ADD_LANGUAGE, new NodeEvent($node));

        return $newNode;
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
     * @return NodeInterface
     */
    public function initializeNewNode()
    {
        $node = new $this->nodeClass();
        $node->setSiteId($this->contextManager->getCurrentSiteId());
        $node->setLanguage($this->contextManager->getCurrentSiteDefaultLanguage());

        $site = $this->siteRepository->findOneBySiteId($this->contextManager->getCurrentSiteId());
        if ($site) {
            $node->setMetaKeywords($site->getMetaKeywords());
            $node->setMetaDescription($site->getMetaDescription());
            $node->setMetaIndex($site->getMetaIndex());
            $node->setMetaFollow($site->getMetaFollow());
            $node->setSitemapPriority($site->getSitemapPriority());
            $node->setSitemapChangefreq($site->getSitemapChangefreq());
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
