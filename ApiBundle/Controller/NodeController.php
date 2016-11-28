<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\ListStatus;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\NewVersionNodeNotGrantedHttpException;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
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
     * @param Request $request
     * @param string $nodeId
     *
     * @return FacadeInterface
     *
     * @Config\Route("/{nodeId}", name="open_orchestra_api_node_show")
     * @Config\Method({"GET"})
     *
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AREAS,
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::PREVIEW,
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::STATUS,
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::NODE_LINKS
     * })
     */
    public function showAction(Request $request, $nodeId)
    {
        $currentSiteManager = $this->get('open_orchestra_backoffice.context_manager');
        $currentSiteDefaultLanguage = $currentSiteManager->getCurrentSiteDefaultLanguage();
        $language = $request->get('language', $currentSiteDefaultLanguage);
        $siteId = $currentSiteManager->getCurrentSiteId();
        $node = $this->findOneNode($nodeId, $language, $siteId, $request->get('version'));
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
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::STATUS,
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::NODE_LINKS,
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::NODE_GENERAL_LINKS
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
                $this->denyAccessUnlessGranted(TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE, $oldNode);
                $node = $this->get('open_orchestra_backoffice.manager.node')->createNewLanguageNode($oldNode, $language);
            } elseif ($errorNode) {
                $this->denyAccessUnlessGranted(TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_ERROR_NODE);
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
        $this->denyAccessUnlessGranted(TreeNodesPanelStrategy::ROLE_ACCESS_DELETE_NODE, $node);

        $this->get('open_orchestra_backoffice.manager.node')->deleteTree($nodes);
        $this->get('object_manager')->flush();

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
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::NODE_LINKS
     * })
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
     *
     * @Config\Route("/list/tree/{siteId}/{language}", name="open_orchestra_api_node_list_tree")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function listTreeNodeAction($siteId, $language)
    {
        $nodes = $this->get('open_orchestra_model.repository.node')->findTreeNode($siteId, $language);
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
     * @param string  $nodeMongoId
     *
     * @Config\Route("/{nodeMongoId}/update", name="open_orchestra_api_node_update")
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function changeStatusAction(Request $request, $nodeMongoId)
    {
        /** @var NodeInterface $node */
        $node = $this->get('open_orchestra_model.repository.node')->find($nodeMongoId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $node);

        return $this->reverseTransform(
            $request, $nodeMongoId,
            'node',
            NodeEvents::NODE_CHANGE_STATUS,
            'OpenOrchestra\ModelInterface\Event\NodeEvent'
        );
    }

    /**
     * @param string $nodeMongoId
     *
     * @Config\Route("/{nodeMongoId}/list-statuses", name="open_orchestra_api_node_list_status")
     * @Config\Method({"GET"})
     *
     * @return Response
     */
    public function listStatusesForNodeAction($nodeMongoId)
    {
        /** @var NodeInterface $node */
        $node = $this->get('open_orchestra_model.repository.node')->find($nodeMongoId);
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
        $this->denyAccessUnlessGranted(TreeNodesPanelStrategy::ROLE_ACCESS_MOVE_TREE);
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
