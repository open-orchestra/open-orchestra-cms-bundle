<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\ListStatus;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\GeneralNodesPanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

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
     * @Config\Route("/{nodeId}", name="open_orchestra_api_node_show")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function showAction(Request $request, $nodeId)
    {
        $currentSiteManager = $this->get('open_orchestra_backoffice.context_manager');
        $currentSiteDefaultLanguage = $currentSiteManager->getCurrentSiteDefaultLanguage();
        $language = $request->get('language', $currentSiteDefaultLanguage);
        $siteId = $currentSiteManager->getCurrentSiteId();
        $node = $this->findOneNode($nodeId, $language, $siteId, $request->get('version'));
        $this->denyAccessUnlessGranted($this->getAccessRole($node), $node);

        return $this->get('open_orchestra_api.transformer_manager')->get('node')->transform($node);
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param bool    $errorNode
     *
     * @Config\Route("/{nodeId}/show-or-create", name="open_orchestra_api_node_show_or_create", defaults={"errorNode" = false})
     * @Config\Route("/{nodeId}/show-or-create-error", name="open_orchestra_api_node_show_or_create_error", defaults={"errorNode" = true})
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function showOrCreateAction(Request $request, $nodeId, $errorNode)
    {
        $currentSiteManager = $this->get('open_orchestra_backoffice.context_manager');
        $currentSiteDefaultLanguage = $currentSiteManager->getCurrentSiteDefaultLanguage();
        $language = $request->get('language', $currentSiteDefaultLanguage);
        $siteId = $currentSiteManager->getCurrentSiteId();
        $node = $this->findOneNode($nodeId, $language, $siteId, $request->get('version'));
        if (!$errorNode && $node) {
            $this->denyAccessUnlessGranted($this->getAccessRole($node));
        }
        if (!$node) {
            $oldNode = $this->findOneNode($nodeId, $currentSiteDefaultLanguage, $siteId);

            if ($oldNode) {
                $this->denyAccessUnlessGranted(TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE, $oldNode);
                $node = $this->get('open_orchestra_backoffice.manager.node')->createNewLanguageNode($oldNode, $language);
            } elseif ($errorNode) {
                $node = $this->get('open_orchestra_backoffice.manager.node')->createNewErrorNode($nodeId, $siteId, $language);
            }

            $dm = $this->get('object_manager');
            $dm->persist($node);

            if ($oldNode) {
                $this->get('open_orchestra_backoffice.manager.node')->updateBlockReferences($oldNode, $node);
            }

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
        $nodes = $this->get('open_orchestra_model.repository.node')->findByNodeAndSite($nodeId, $siteId);

        $node = !empty($nodes) ? $nodes[0] : null;
        $this->denyAccessUnlessGranted(TreeNodesPanelStrategy::ROLE_ACCESS_DELETE_NODE, $node);

        $this->get('open_orchestra_backoffice.manager.node')->deleteTree($nodes);
        $this->get('object_manager')->flush();

        return array();
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     *
     * @Config\Route("/{nodeId}/duplicate/{version}", name="open_orchestra_api_node_duplicate", defaults={"version": null})
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function duplicateAction(Request $request, $nodeId, $version = null)
    {
        $language = $request->get('language');
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $node = $this->findOneNode($nodeId, $language, $siteId);

        $this->denyAccessUnlessGranted($this->getEditionRole($node), $node);

        $newNode = $this->get('open_orchestra_backoffice.manager.node')->duplicateNode($nodeId, $siteId, $language, $version);
        $this->dispatchEvent(NodeEvents::NODE_DUPLICATE, new NodeEvent($newNode));

        return array();
    }

    /**
     * @param boolean|null $published
     *
     * @Config\Route("/list/not-published-by-author", name="open_orchestra_api_node_list_author_and_site_not_published", defaults={"published": false})
     * @Config\Route("/list/by-author", name="open_orchestra_api_node_list_author_and_site", defaults={"published": null})
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function listNodeByAuthorAndSiteIdAction($published)
    {
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $nodes = $this->get('open_orchestra_model.repository.node')->findByAuthorAndSiteId(
            $user->getUsername(),
            $siteId,
            $published,
            10,
            array('createdAt' => -1)
        );

        return $this->get('open_orchestra_api.transformer_manager')->get('node_collection')->transform($nodes);
    }

    /**
     * @param string $siteId
     *
     * @Config\Route("/list/tree/{siteId}", name="open_orchestra_api_node_list_tree")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_TREE_NODE')")
     *
     * @return FacadeInterface
     */
    public function listTreeNode($siteId)
    {
        $nodes = $this->get('open_orchestra_model.repository.node')->findLastVersionByType($siteId);
        if (empty($nodes)) {
            return array();
        }

        $orderedNodes = $this->get('open_orchestra_display.manager.tree')->generateTree($nodes);

        return $this->get('open_orchestra_api.transformer_manager')->get('node_tree')->transform(end($orderedNodes));
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
        $this->denyAccessUnlessGranted($this->getAccessRole($node), $node);

        return $this->get('open_orchestra_api.transformer_manager')->get('node_collection')->transformVersions($nodes);
    }

    /**
     * @param Request $request
     * @param string  $nodeMongoId
     *
     * @Config\Route("/{nodeMongoId}/update", name="open_orchestra_api_node_update")
     * @Config\Method({"POST"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_NODE')")
     *
     * @return Response
     */
    public function changeStatusAction(Request $request, $nodeMongoId)
    {
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
     * @Config\Security("is_granted('ROLE_ACCESS_TREE_NODE')")
     *
     * @return Response
     */
    public function listStatusesForNodeAction($nodeMongoId)
    {
        /** @var NodeInterface $node */
        $node = $this->get('open_orchestra_model.repository.node')->find($nodeMongoId);
        if (!$node->getNodeType() === NodeInterface::TYPE_ERROR) {
            $this->denyAccessUnlessGranted(TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE, $node);
        }

        return $this->listStatuses($node);
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     *
     * @Config\Route("/{nodeId}/children/update/order", name="open_orchestra_api_node_update_children_order")
     * @Config\Method({"POST"})
     * @Api\Serialize()
     *
     * @return Response
     */
    public function updateChildrenOrderAction(Request $request, $nodeId)
    {
        $node = $this->get('open_orchestra_model.repository.node')->findOneByNodeId($nodeId);
        $this->denyAccessUnlessGranted(TreeNodesPanelStrategy::ROLE_ACCESS_MOVE_NODE, $node);
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

    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    protected function getAccessRole(NodeInterface $node)
    {
        if (NodeInterface::TYPE_TRANSVERSE === $node->getNodeType()) {
            return GeneralNodesPanelStrategy::ROLE_ACCESS_TREE_GENERAL_NODE;
        }

        return TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE;
    }

    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    protected function getEditionRole(NodeInterface $node)
    {
        if (NodeInterface::TYPE_TRANSVERSE === $node->getNodeType()) {
            return GeneralNodesPanelStrategy::ROLE_ACCESS_UPDATE_GENERAL_NODE;
        }

        return TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE;
    }
}
