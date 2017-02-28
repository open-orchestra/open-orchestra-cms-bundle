<?php

namespace OpenOrchestra\ApiBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ApiBundle\Controller\ControllerTrait\ListStatus;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\AreaNotFoundHttpException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\BlockNotFoundHttpException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\NodeNotFoundHttpException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\StatusChangeNotGrantedHttpException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\BlockEvents;
use OpenOrchestra\ModelInterface\BlockNodeEvents;
use OpenOrchestra\ModelInterface\Event\BlockEvent;
use OpenOrchestra\ModelInterface\Event\BlockNodeEvent;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class NodeController
 *
 * @Config\Route("node")
 *
 * @Api\Serialize()
 */
class NodeController extends BaseController
{
    use ListStatus;

    /**
     * @param string $nodeId
     * @param string $siteId
     * @param string $language
     * @param string $version
     *
     * @return FacadeInterface
     *
     * @Config\Route(
     *     "/show/{nodeId}/{siteId}/{language}/{version}",
     *     name="open_orchestra_api_node_show",
     *     defaults={"version": null},
     * )
     * @Config\Method({"GET"})
     *
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AREAS,
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::PREVIEW,
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::STATUS,
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AUTHORIZATIONS
     * })
     *
     * @throws NodeNotFoundHttpException
     */
    public function showAction($nodeId, $siteId, $language, $version)
    {
        $node = $this->findOneNode($nodeId, $language, $siteId, $version);
        if (!$node instanceof NodeInterface) {
            throw new NodeNotFoundHttpException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, $node);

        return $this->get('open_orchestra_api.transformer_manager')->get('node')->transform($node);
    }

    /**
     * @param string $nodeId
     *
     * @Config\Route("/{nodeId}/delete", name="open_orchestra_api_node_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($nodeId)
    {
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $nodes = $this->get('open_orchestra_model.repository.node')->findByNodeAndSiteSortedByVersion($nodeId, $siteId);
        $node = !empty($nodes) ? $nodes[0] : null;
        $this->denyAccessUnlessGranted(ContributionActionInterface::DELETE, $node);

        $this->get('open_orchestra_backoffice.manager.node')->deleteTree($nodes);
        $this->get('object_manager')->flush();

        return array();
    }

    /**
     * @param string  $nodeId
     * @param string  $siteId
     * @param string  $language
     * @param string  $version
     * @param string  $areaName
     * @param string  $blockId
     *
     * @Config\Route("/delete-block/{nodeId}/{siteId}/{language}/{version}/{areaName}/{blockId}", name="open_orchestra_api_node_delete_block")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     * @throws NodeNotFoundHttpException
     */
    public function deleteBlockInAreaAction($nodeId, $siteId, $language, $version, $blockId, $areaName)
    {
        $node = $this->findOneNode($nodeId, $language, $siteId, $version);
        if (!$node instanceof NodeInterface) {
            throw new NodeNotFoundHttpException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $node);

        if ($node->getStatus()->isBlockedEdition()) {
            return array();
        }

        $block = $this->get('open_orchestra_model.repository.block')->findById($blockId);

        if (!$block instanceof BlockInterface) {
            throw new NodeNotFoundHttpException();
        }

        $this->get('open_orchestra_model.repository.node')->removeBlockInArea($blockId, $areaName, $nodeId, $siteId, $language, $version);

        if (false === $block->isTransverse()) {
            $objectManager = $this->get('object_manager');
            $objectManager->remove($block);
            $objectManager->flush();
            $this->dispatchEvent(BlockEvents::POST_BLOCK_DELETE, new BlockEvent($block));
        }

        return array();
    }

    /**
     * @param string  $nodeId
     * @param string  $language
     * @param string  $version
     * @param string  $blockId
     * @param string  $areaId
     * @param string  $position
     *
     * @Config\Route(
     *     "/add-block-in-area/{nodeId}/{language}/{version}/{blockId}/{areaId}/{position}",
     *     requirements={"position": "\d+"},
     *     name="open_orchestra_node_add_block"
     * )
     * @Config\Method({"PUT"})
     *
     * @return FacadeInterface
     *
     * @throws NodeNotFoundHttpException
     * @throws BlockNotFoundHttpException
     */
    public function addBlockInAreaAction($nodeId, $language, $version, $blockId, $areaId, $position)
    {
        $position = (int) $position;
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $node = $this->findOneNode($nodeId, $language, $siteId, $version);
        if (!$node instanceof NodeInterface) {
            throw new NodeNotFoundHttpException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $node);

        $area = $node->getArea($areaId);
        $block = $this->get('open_orchestra_model.repository.block')->findById($blockId);
        if (!$block instanceof BlockInterface) {
            throw new BlockNotFoundHttpException();
        }

        $area->addBlock($block, $position);

        $objectManager = $this->get('object_manager');
        $objectManager->persist($node);
        $objectManager->flush();
        $this->dispatchEvent(BlockNodeEvents::ADD_BLOCK_TO_NODE, new BlockNodeEvent($node, $block));

        return array();
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param string  $language
     * @param string  $version
     * @param string  $areaId
     *
     * @Config\Route(
     *     "/copy-translated-blocks-in-area/{nodeId}/{language}/{version}/{areaId}",
     *     name="open_orchestra_node_copy_blocks_in_area")

     * @Config\Method({"PATCH"})
     *
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AREAS,
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AUTHORIZATIONS
     * })
     *
     * @return FacadeInterface
     *
     * @throws NodeNotFoundHttpException
     * @throws AreaNotFoundHttpException
     */
    public function copyTranslatedBlocksInAreaAction(Request $request, $nodeId, $language, $version, $areaId)
    {
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $node = $this->findOneNode($nodeId, $language, $siteId, $version);
        if (!$node instanceof NodeInterface) {
            throw new NodeNotFoundHttpException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $node);

        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'OpenOrchestra\ApiBundle\Facade\AreaCollectionFacade',
            $request->get('_format', 'json')
        );

        if (!array_key_exists($areaId, $facade->getAreas()) || null === $node->getArea($areaId)) {
            throw new AreaNotFoundHttpException();
        }

        $areaFacade = $facade->getAreas()[$areaId];
        $area = $this->get('open_orchestra_api.transformer_manager')->get('area')->reverseTransform($areaFacade);
        $blocks = $area->getBlocks();
        $objectManager = $this->get('object_manager');
        /** @var BlockInterface $block */
        foreach ($blocks as $block) {
            if (false === $block->isTransverse()) {
                $blockToTranslate = $this->get('open_orchestra_backoffice.manager.block')->createToTranslateBlock($block, $language);
                $node->getArea($areaId)->addBlock($blockToTranslate);
                $objectManager->persist($blockToTranslate);

                $this->dispatchEvent(BlockNodeEvents::ADD_BLOCK_TO_NODE, new BlockNodeEvent($node, $blockToTranslate));
                $this->dispatchEvent(BlockEvents::POST_BLOCK_CREATE, new BlockEvent($blockToTranslate));
            }
        }

        $objectManager->persist($node);
        $objectManager->flush();

        return $this->get('open_orchestra_api.transformer_manager')->get('node')->transform($node);
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param string  $language
     * @param string  $version
     * @param string  $siteId
     *
     * @Config\Route("/update-block-position/{siteId}/{nodeId}/{version}/{language}", name="open_orchestra_node_update_block_position")
     * @Config\Method({"PUT"})
     *
     * @return FacadeInterface
     *
     * @throws NodeNotFoundHttpException
     */
    public function updateBlockPositionAction(Request $request, $nodeId, $language, $version, $siteId)
    {
        $node = $this->findOneNode($nodeId, $language, $siteId, $version);
        if (!$node instanceof NodeInterface) {
            throw new NodeNotFoundHttpException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $node);

        if ($node->getStatus()->isBlockedEdition()) {
            return array();
        }

        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'OpenOrchestra\ApiBundle\Facade\NodeFacade',
            $request->get('_format', 'json')
        );

        $areaClass = $this->getParameter('open_orchestra_model.document.area.class');
        $updatedBlock = array();
        foreach ($facade->getAreas() as $key => $facadeArea) {
            $blocks = new ArrayCollection();

            foreach ($facadeArea->getBlocks() as $block) {
                $block = $this->get('open_orchestra_model.repository.block')->findById($block->id);
                $blocks[] = $block;
                $updatedBlock[] = $block;
            }
            /** @var AreaInterface $area */
            $area = new $areaClass();
            $area->setBlocks($blocks);
            $node->setArea($key, $area);
        }

        $this->get('object_manager')->flush();
        $this->dispatchEvent(NodeEvents::NODE_UPDATE_BLOCK_POSITION, new NodeEvent($node, null, $updatedBlock));

        return array();
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param string  $language
     * @param string  $originalVersion
     *
     * @Config\Route("/new-version/{nodeId}/{language}/{originalVersion}", name="open_orchestra_api_node_new_version")
     * @Config\Method({"POST"})
     *
     * @return Response
     *
     * @throws NodeNotFoundHttpException
     */
    public function newVersionAction(Request $request, $nodeId, $language, $originalVersion)
    {
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $originalNodeVersion = $this->findOneNode($nodeId, $language, $siteId, $originalVersion);
        if (!$originalNodeVersion instanceof NodeInterface) {
            throw new NodeNotFoundHttpException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $originalNodeVersion);

        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'OpenOrchestra\ApiBundle\Facade\NodeFacade',
            $request->get('_format', 'json')
        );
        $nodeManager = $this->get('open_orchestra_backoffice.manager.node');
        $newNode = $nodeManager->createNewVersionNode($originalNodeVersion, $facade->versionName);

        $objectManager = $this->get('object_manager');
        $objectManager->persist($newNode);
        $objectManager->flush();
        $this->dispatchEvent(NodeEvents::NODE_DUPLICATE, new NodeEvent($newNode));

        return array();
    }

    /**
     * @param boolean|null $published
     *
     * @return FacadeInterface
     *
     * @Config\Route("/list/not-published-by-author", name="open_orchestra_api_node_list_author_and_site_not_published", defaults={"published": false})
     * @Config\Route("/list/by-author", name="open_orchestra_api_node_list_author_and_site", defaults={"published": null})
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function listNodeByAuthorAndSiteIdAction($published)
    {
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $nodes = $this->get('open_orchestra_model.repository.node')->findByHistoryAndSiteId(
            $user->getId(),
            $siteId,
            array(NodeEvents::NODE_CREATION, NodeEvents::NODE_UPDATE),
            $published,
            10,
            array('histories.updatedAt' => -1)
        );

        return $this->get('open_orchestra_api.transformer_manager')->get('node_collection')->transform($nodes);
    }

    /**
     * @param string $nodeId
     * @param string $siteId
     * @param string $areaId
     * @Config\Route("/list/with-block-in-area/{nodeId}/{siteId}/{areaId}", name="open_orchestra_api_node_list_with_block_in_area")
     *
     * @Config\Method({"GET"})
     * @Config\Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AREAS,
     * })
     * @return FacadeInterface
     */
    public function listNodeWithBlockInAreaAction($nodeId, $siteId, $areaId)
    {
        $nodeRepository = $this->get('open_orchestra_model.repository.node');
        $nodes = $nodeRepository->findByNodeIdAndSiteIdWithBlocksInArea($nodeId, $siteId, $areaId);

        return $this->get('open_orchestra_api.transformer_manager')->get('node_collection')->transform($nodes);
    }

    /**
     * @param String  $siteId
     * @param String  $language
     * @param String  $parentId
     *
     * @Config\Route(
     *     "/list/tree/{siteId}/{language}/{parentId}",
     *     defaults={"parentId": NodeInterface::ROOT_PARENT_ID},
     *     name="open_orchestra_api_node_list_tree"
     * )
     * @Config\Method({"GET"})
     * @Config\Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @return FacadeInterface
     */
    public function listTreeNodeAction($siteId, $language, $parentId = NodeInterface::ROOT_PARENT_ID)
    {
        $nodes = $this->get('open_orchestra_model.repository.node')->findTreeNode($siteId, $language, $parentId);
        if(empty($nodes)) {
            return array();
        }

        return $this->get('open_orchestra_api.transformer_manager')->get('nodes_tree')->transform($nodes);
    }

    /**
     * @param string  $nodeId
     * @param string  $language
     *
     * @Config\Route("/list-version/{nodeId}/{language}", name="open_orchestra_api_node_list_version")
     * @Config\Method({"GET"})
     * @Config\Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::STATUS,
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AUTHORIZATIONS_DELETE_VERSION
     * })
     * @return Response
     */
    public function listVersionAction($nodeId, $language)
    {
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $nodes = $this->get('open_orchestra_model.repository.node')->findNotDeletedSortByUpdatedAt($nodeId, $language, $siteId);

        return $this->get('open_orchestra_api.transformer_manager')->get('node_collection')->transform($nodes);
    }

    /**
     * @param Request $request
     * @param boolean $saveOldPublishedVersion
     *
     * @Config\Route(
     *     "/update-status",
     *     name="open_orchestra_api_node_update_status",
     *     defaults={"saveOldPublishedVersion": false},
     * )
     * @Config\Route(
     *     "/update-status-with-save-published-version",
     *     name="open_orchestra_api_node_update_status_with_save_published",
     *     defaults={"saveOldPublishedVersion": true},
     * )
     * @Config\Method({"PUT"})
     *
     * @return Response
     * @throws NodeNotFoundHttpException
     * @throws StatusChangeNotGrantedHttpException
     */
    public function changeStatusAction(Request $request, $saveOldPublishedVersion)
    {
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'OpenOrchestra\ApiBundle\Facade\NodeFacade',
            $request->get('_format', 'json')
        );

        $nodeRepository = $this->get('open_orchestra_model.repository.node');
        $node = $nodeRepository->find($facade->id);
        if (!$node instanceof NodeInterface) {
            throw new NodeNotFoundHttpException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $node);
        $nodeSource = clone $node;

        $this->get('open_orchestra_api.transformer_manager')->get('node')->reverseTransform($facade, $node);
        $status = $node->getStatus();
        if ($status !== $nodeSource->getStatus()) {
            if (!$this->isGranted($status, $nodeSource)) {
                throw new StatusChangeNotGrantedHttpException();
            }

            if (true === $status->isPublishedState() && false === $saveOldPublishedVersion) {
                $oldPublishedVersion = $nodeRepository->findOnePublished(
                    $node->getNodeId(),
                    $node->getLanguage(),
                    $node->getSiteId()
                );
                if ($oldPublishedVersion instanceof NodeInterface) {
                    $this->get('object_manager')->remove($oldPublishedVersion);
                }
            }

            $this->get('object_manager')->flush();
            $event = new NodeEvent($node, $nodeSource->getStatus());
            $this->dispatchEvent(NodeEvents::NODE_CHANGE_STATUS, $event);
        }

        return array();
    }

    /**
     * @param string $nodeId
     * @param string $siteId
     * @param string $language
     * @param string $version
     *
     * @Config\Route(
     *     "/list-statuses/{nodeId}/{siteId}/{language}/{version}",
     *     name="open_orchestra_api_node_list_status")
     * @Config\Method({"GET"})
     *
     * @return Response
     * @throws NodeNotFoundHttpException
     */
    public function listStatusesForNodeAction($nodeId, $language, $siteId, $version)
    {
        $node = $this->findOneNode($nodeId, $language, $siteId, $version);
        if (!$node instanceof NodeInterface) {
            throw new NodeNotFoundHttpException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, $node);

        return $this->listStatuses($node);
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     *
     * @Config\Route("/{nodeId}/children/update/order", name="open_orchestra_api_node_update_children_order")
     * @Config\Method({"PUT"})
     * @Api\Serialize()
     *
     * @return Response
     */
    public function updateChildrenOrderAction(Request $request, $nodeId)
    {
        $node = $this->get('open_orchestra_model.repository.node')->findOneByNodeId($nodeId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $node);
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'OpenOrchestra\ApiBundle\Facade\NodeCollectionFacade',
            $request->get('_format', 'json')
        );

        $orderedNode = $this->get('open_orchestra_api.transformer_manager')->get('node_collection')->reverseTransformOrder($facade);
        $this->get('open_orchestra_backoffice.manager.node')->orderNodeChildren($orderedNode, $node);

        $this->get('object_manager')->flush();

        return array();
    }

    /**
     * @param Request $request
     * @param String  $siteId
     * @param String  $language
     *
     * @Config\Route("/list/{siteId}/{language}", name="open_orchestra_api_node_list")
     * @Config\Method({"GET"})
     * @Config\Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::STATUS,
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AUTHORIZATIONS
     * })
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request, $siteId, $language)
    {
        $mapping = array(
            'updated_at' => 'updatedAt',
            'name'      => 'name',
            'created_by'=> 'createdBy',
            'status.label' => 'status.labels',
        );
        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);

        $repository = $this->get('open_orchestra_model.repository.node');
        $collection = $repository->findForPaginate($configuration, $siteId, $language);
        $recordsTotal = $repository->count($siteId, $language);
        $recordsFiltered = $repository->countWithFilter($configuration, $siteId, $language);

        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('node_collection');
        $facade = $collectionTransformer->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param string  $language
     *
     * @Config\Route("/delete-multiple-version/{nodeId}/{language}", name="open_orchestra_api_node_delete_multiple_versions")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteNodeVersionsAction(Request $request, $nodeId, $language)
    {
        $format = $request->get('_format', 'json');

        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            $this->getParameter('open_orchestra_api.facade.node_collection.class'),
            $format
        );
        $nodes = $this->get('open_orchestra_api.transformer_manager')->get('node_collection')->reverseTransform($facade);

        $nodeRepository = $this->get('open_orchestra_model.repository.node');
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $versionsCount = $nodeRepository->countNotDeletedVersions($nodeId, $language, $siteId);
        if ($versionsCount > count($nodes)) {
            $nodeIds = array();
            foreach ($nodes as $node) {
                if ($this->isGranted(ContributionActionInterface::DELETE, $node) &&
                    !$node->getStatus()->isPublishedState()
                ) {
                    $this->get('open_orchestra_backoffice.manager.node')->deleteBlockInNode($node);
                    $nodeIds[] = $node->getId();
                    $this->dispatchEvent(NodeEvents::NODE_DELETE_VERSION, new NodeEvent($node));
                }
            }
            $nodeRepository->removeNodeVersions($nodeIds);
            $this->get('object_manager')->flush();
        }

        return array();
    }

    /**
     * @param Request $request
     * @param String  $siteId
     * @param String  $language
     * @param String  $blockId
     *
     * @Config\Route("/list-usage-block/{siteId}/{language}/{blockId}", name="open_orchestra_api_node_list_usage_block")
     * @Config\Method({"GET"})
     * @Config\Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     *  @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::STATUS
     * })
     *
     * @return FacadeInterface
     */
    public function listUsageBlockAction(Request $request, $siteId, $language, $blockId)
    {
        $mapping = array(
            'updated_at' => 'updatedAt',
            'name'      => 'name',
            'created_by'=> 'createdBy',
            'status.label' => 'status.labels',
            'version'   => 'version'
        );
        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);

        $repository = $this->get('open_orchestra_model.repository.node');
        $collection = $repository->findWithBlockUsedForPaginate($configuration, $siteId, $language, $blockId);
        $recordsTotal = $repository->countWithBlockUsed($siteId, $language, $blockId);
        $recordsFiltered = $recordsTotal;

        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('node_collection');
        $facade = $collectionTransformer->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }

    /**
     * @param string   $nodeId
     * @param string   $language
     * @param string   $siteId
     * @param int|null $version
     *
     * @return NodeInterface|null
     */
    protected function findOneNode($nodeId, $language, $siteId, $version = null)
    {
        if (null !== $version) {
            return $this->get('open_orchestra_model.repository.node')->findVersionNotDeleted($nodeId, $language, $siteId, $version);
        }

        return $this->get('open_orchestra_model.repository.node')->findInLastVersion($nodeId, $language, $siteId);
    }
}
