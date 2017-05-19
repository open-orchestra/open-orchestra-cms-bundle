<?php

namespace OpenOrchestra\Backoffice\Manager;

use OpenOrchestra\Backoffice\Context\ContextBackOfficeInterface;
use OpenOrchestra\Backoffice\Util\UniqueIdGenerator;
use OpenOrchestra\ModelInterface\BlockEvents;
use OpenOrchestra\ModelInterface\Event\BlockEvent;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\ReadNodeInterface;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\SiteRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OpenOrchestra\ModelInterface\Repository\BlockRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class NodeManager
 */
class NodeManager
{
    protected $statusRepository;
    protected $blockRepository;
    protected $eventDispatcher;
    protected $nodeRepository;
    protected $siteRepository;
    protected $contextManager;
    protected $templateManager;
    protected $nodeClass;
    protected $areaClass;
    protected $uniqueIdGenerator;
    protected $tokenStorage;

    /**
     * Constructor
     *
     * @param NodeRepositoryInterface    $nodeRepository
     * @param SiteRepositoryInterface    $siteRepository
     * @param StatusRepositoryInterface  $statusRepository
     * @param BlockRepositoryInterface   $blockRepository
     * @param ContextBackOfficeInterface $contextManager
     * @param string                     $nodeClass
     * @param string                     $areaClass
     * @param EventDispatcherInterface   $eventDispatcher
     * @param TemplateManager            $templateManager
     * @param UniqueIdGenerator          $uniqueIdGenerator
     * @param TokenStorage               $tokenStorage
     */
    public function __construct(
        NodeRepositoryInterface $nodeRepository,
        SiteRepositoryInterface $siteRepository,
        StatusRepositoryInterface $statusRepository,
        BlockRepositoryInterface  $blockRepository,
        ContextBackOfficeInterface $contextManager,
        TemplateManager $templateManager,
        $nodeClass,
        $areaClass,
        $eventDispatcher,
        UniqueIdGenerator $uniqueIdGenerator,
        TokenStorage $tokenStorage
    ){
        $this->nodeRepository = $nodeRepository;
        $this->siteRepository = $siteRepository;
        $this->statusRepository = $statusRepository;
        $this->blockRepository = $blockRepository;
        $this->contextManager = $contextManager;
        $this->nodeClass = $nodeClass;
        $this->areaClass = $areaClass;
        $this->eventDispatcher = $eventDispatcher;
        $this->templateManager = $templateManager;
        $this->uniqueIdGenerator = $uniqueIdGenerator;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Duplicate a node
     *
     * @param NodeInterface $originalNode
     * @param string        $versionName
     *
     * @return NodeInterface
     */
    public function createNewVersionNode(NodeInterface $originalNode, $versionName = '')
    {
        $status = $this->statusRepository->findOneByInitial();

        /** @var NodeInterface $newNode */
        $newNode = clone $originalNode;
        $newNode->setStatus($status);
        $newNode->setVersion($this->uniqueIdGenerator->generateUniqueId());
        $this->duplicateArea($originalNode, $newNode);

        $newNode->setVersionName($versionName);
        if (empty($versionName)) {
            $newNode = $this->setVersionName($newNode);
        }


        return $newNode;
    }

    /**
     * @param string $nodeId
     * @param string $parentId
     * @param string $siteId
     * @param string $language
     * @param string $template
     * @param string $name
     *
     * @return NodeInterface
     */
    public function createNewErrorNode($nodeId, $name, $parentId, $siteId, $language, $template)
    {
        $node = $this->initializeNode(NodeInterface::ROOT_NODE_ID, $language, $siteId);
        $node->setNodeId($nodeId);
        $node->setParentId($parentId);
        $node->setNodeType(ReadNodeInterface::TYPE_ERROR);
        $node->setName($name);
        $node->setInFooter(false);
        $node->setInMenu(false);
        $node->setTemplate($template);
        $node->setOrder(-1);
        $node = $this->setVersionName($node);

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
        $newNode->setVersion($this->uniqueIdGenerator->generateUniqueId());
        $status = $this->statusRepository->findOneByTranslationState();

        $newNode->setStatus($status);
        $newNode = $this->duplicateArea($node, $newNode, false);

        $newNode->setLanguage($language);
        $newNode->setSeoTitle(null);
        $newNode->setMetaDescription(null);
        $newNode->setMetaIndex(false);
        $newNode->setMetaFollow(false);
        $newNode->setSitemapChangefreq(null);
        $newNode->setSitemapPriority(null);
        $newNode->initializeKeywords();

        $this->eventDispatcher->dispatch(NodeEvents::NODE_ADD_LANGUAGE, new NodeEvent($node));

        return $newNode;
    }

    /**
     * @param NodeInterface $node
     */
    public function deleteBlockInNode(NodeInterface $node)
    {
        foreach ($node->getAreas() as $area) {
            foreach ($area->getBlocks() as $block) {
                if (!$block->isTransverse()) {
                    $this->blockRepository->getDocumentManager()->remove($block);
                    $this->eventDispatcher->dispatch(BlockEvents::POST_BLOCK_DELETE, new BlockEvent($block));
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
        $siteId = $this->contextManager->getSiteId();
        $oldNode = $this->nodeRepository->findInLastVersion($nodeId, $node->getLanguage(), $siteId);
        if ($oldNode) {
            $node->setTemplate($oldNode->getTemplate());
            $this->duplicateArea($oldNode, $node);
        }

        return $node;
    }

    /**
     * @param NodeInterface $node
     * @param NodeInterface $newNode
     * @param boolean       $duplicateBlock
     *
     * @return NodeInterface
     */
    protected function duplicateArea(NodeInterface $node, NodeInterface $newNode, $duplicateBlock = true)
    {
        foreach ($node->getAreas() as $areaId => $area) {
            $newArea = clone $area;
            $newNode->setArea($areaId, $newArea);
            if (true === $duplicateBlock) {
                foreach ($area->getBlocks() as $block) {
                    if (!$block->isTransverse()) {
                        $newBlock = clone $block;
                        $this->blockRepository->getDocumentManager()->persist($newBlock);
                        $this->eventDispatcher->dispatch(BlockEvents::POST_BLOCK_CREATE, new BlockEvent($newBlock));
                        $newArea->addBlock($newBlock);
                    } else {
                        $newArea->addBlock($block);
                    }
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
        $node->setInMenu(true);
        $node->setInFooter(true);
        $node = $this->setVersionName($node);

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
        $node->setVersion($this->uniqueIdGenerator->generateUniqueId());
        $node->setCreatedBy($this->tokenStorage->getToken()->getUser()->getUsername());

        $parentNode = $this->nodeRepository->findInLastVersion($parentId, $language, $siteId);
        $status = $this->statusRepository->findOneByInitial();
        $node->setStatus($status);
        $nodeType = NodeInterface::TYPE_DEFAULT;
        if ($parentNode instanceof NodeInterface) {
            $nodeType = $parentNode->getNodeType();
            $node->setPath($parentNode->getPath());
        } else {
            $node->setNodeId(NodeInterface::ROOT_NODE_ID);
        }
        $node->setNodeType($nodeType);

        return $node;
    }

    /**
     * @param NodeInterface $node
     *
     * @return NodeInterface
     */
    public function initializeAreasNode(NodeInterface $node)
    {
        $site = $this->siteRepository->findOneBySiteId($node->getSiteId());
        $templateSet = $site->getTemplateSet();
        $areasName = $this->templateManager->getTemplateAreas($node->getTemplate(), $templateSet);
        foreach($areasName as $areaName) {
            if (! $node->getArea($areaName) instanceof AreaInterface) {
                $node->setArea($areaName, new $this->areaClass());
            }
        }

        return $node;
    }

    /**
     * @param NodeInterface $node
     *
     * @return NodeInterface
     */
    public function setVersionName(NodeInterface $node)
    {
        $date = new \DateTime("now");
        $versionName = $node->getName() . '_' . $date->format("Y-m-d_H:i:s");
        $node->setVersionName($versionName);

        return $node;
    }

    /**
     * 
     * @param array         $orderedNodes
     * @param NodeInterface $parentNode
     */
    public function reorderNodes(array $orderedNodes, NodeInterface $parentNode)
    {
        foreach ($orderedNodes as $position => $nodeId) {
            $nodeVersions = $this->nodeRepository->findByNodeAndSite($nodeId, $parentNode->getSiteId());

            if (is_array($nodeVersions) && count($nodeVersions) > 0) {
                $this->updateNodeVersionsOrder($nodeVersions, $position);

                if ($nodeVersions[0]->getParentId() !== $parentNode->getNodeId()) {
                    $this->moveNodeVersions($nodeVersions, $parentNode);
                }
            }
        }
    }

    /**
     * @param array $nodeVersions
     * @param int   $position
     */
    protected function updateNodeVersionsOrder(array $nodeVersions, $position)
    {
        foreach ($nodeVersions as $nodeVersion) {
            if ($nodeVersion instanceof NodeInterface) {
                $nodeVersion->setOrder($position);
            }
        }
    }

    /**
     * @param array         $nodeVersions
     * @param NodeInterface $parentNode
     */
    protected function moveNodeVersions(array $nodeVersions, NodeInterface $parentNode)
    {
        $event = null;

        foreach ($nodeVersions as $nodeVersion) {
            if ($nodeVersion instanceof NodeInterface) {
                $oldPath = $nodeVersion->getPath();

                $nodeVersion->setParentId($parentNode->getNodeId());
                $nodeVersion->setPath($parentNode->getPath() . '/' . $nodeVersion->getNodeId());

                if (is_null($event)) {
                    $event = new NodeEvent($nodeVersion);
                    $event->setPreviousPath($oldPath);
                }
            }
        }

        if (!is_null($event)) {
            $this->eventDispatcher->dispatch(NodeEvents::PATH_UPDATED, $event);
        }
    }
}
