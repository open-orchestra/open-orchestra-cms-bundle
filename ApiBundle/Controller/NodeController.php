<?php

namespace OpenOrchestra\ApiBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ApiBundle\Controller\ControllerTrait\ListStatus;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\NewVersionNodeNotGrantedHttpException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\NodeNotFoundHttpException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\StatusChangeNotGrantedHttpException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\AccessLanguageForNodeNotGrantedHttpException;
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
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::STATUS
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
     * @param Request $request
     * @param string  $nodeId
     * @param bool    $errorNode
     *
     * @return FacadeInterface
     * @throws AccessLanguageForNodeNotGrantedHttpException
     *
     * @Config\Route("/{nodeId}/show-or-create", name="open_orchestra_api_node_show_or_create", defaults={"errorNode" = false})
     * @Config\Route("/{nodeId}/show-or-create-error", name="open_orchestra_api_node_show_or_create_error", defaults={"errorNode" = true})
     * @Config\Method({"GET"})
     *
     *  @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AREAS,
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::PREVIEW,
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::STATUS
     * })
     */
    public function showOrCreateAction(Request $request, $nodeId, $errorNode)
    {
        $currentSiteManager = $this->get('open_orchestra_backoffice.context_manager');
        $currentSiteDefaultLanguage = $currentSiteManager->getCurrentSiteDefaultLanguage();
        $language = $request->get('language', $currentSiteDefaultLanguage);
        $siteId = $currentSiteManager->getCurrentSiteId();
        $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);

        if (!is_null($site)) {
            if (!in_array($language, $site->getLanguages())) {
                throw new AccessLanguageForNodeNotGrantedHttpException();
            }
        }

        $node = $this->findOneNode($nodeId, $language, $siteId, $request->get('version'));
        if ($node) {
            $this->denyAccessUnlessGranted(ContributionActionInterface::READ, $node);
        }
        if (!$node) {
            $oldNode = $this->findOneNode($nodeId, $currentSiteDefaultLanguage, $siteId);

            if ($oldNode) {
                $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, $oldNode);
                $node = $this->get('open_orchestra_backoffice.manager.node')->createNewLanguageNode($oldNode, $language);
            } elseif ($errorNode) {
                $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, $errorNode);
                $node = $this->get('open_orchestra_backoffice.manager.node')->createNewErrorNode($nodeId, $siteId, $language);
            }

            $dm = $this->get('object_manager');
            $dm->persist($node);

            $dm->flush();
        }

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
        $version = (int) $version;
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
        }

        return array();
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
        $version = (int) $version;
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
     * @param Request      $request
     * @param string       $nodeId
     * @param string|null  $version
     *
     * @Config\Route("/{nodeId}/new-version/{version}", name="open_orchestra_api_node_new_version", defaults={"version": null})
     * @Config\Method({"POST"})
     *
     * @return Response
     *
     * @throws NewVersionNodeNotGrantedHttpException
     */
    public function newVersionAction(Request $request, $nodeId, $version = null)
    {
        $language = $request->get('language');
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $nodeManager = $this->get('open_orchestra_backoffice.manager.node');
        $newNode = $nodeManager->duplicateNode($nodeId, $siteId, $language, $version, false);
        try{
            $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $newNode);
        } catch(AccessDeniedException $exception) {
            throw new NewVersionNodeNotGrantedHttpException();
        }
        $nodeManager->saveDuplicatedNode($newNode);
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
     * @param Request $request
     * @param string  $nodeId
     *
     * @Config\Route("/{nodeId}/list-version", name="open_orchestra_api_node_list_version")
     * @Config\Method({"GET"})
     *
     * @return Response
     */
    public function listVersionAction(Request $request, $nodeId)
    {
        $language = $request->get('language');
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $nodes = $this->get('open_orchestra_model.repository.node')->findByNodeAndLanguageAndSite($nodeId, $language, $siteId);
        $node = !empty($nodes) ? $nodes[0] : null;
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, $node);

        return $this->get('open_orchestra_api.transformer_manager')->get('node_collection')->transformVersions($nodes);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/update-status", name="open_orchestra_api_node_update_status")
     * @Config\Method({"PUT"})
     *
     * @return Response
     * @throws NodeNotFoundHttpException
     * @throws StatusChangeNotGrantedHttpException
     */
    public function changeStatusAction(Request $request)
    {
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'OpenOrchestra\ApiBundle\Facade\NodeFacade',
            $request->get('_format', 'json')
        );

        $node = $this->get('open_orchestra_model.repository.node')->find($facade->id);
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
            $event = new NodeEvent($node, $nodeSource->getStatus());
            $this->dispatchEvent(NodeEvents::NODE_CHANGE_STATUS, $event);
            $this->get('object_manager')->flush();
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
     *  @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::STATUS
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
        $node = $this->get('open_orchestra_model.repository.node')->findVersion($nodeId, $language, $siteId, $version);

        return $node;
    }
}
