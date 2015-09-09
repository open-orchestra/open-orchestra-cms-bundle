<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\ListStatus;
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
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
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
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
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

        if (!$node) {
            $oldNode = $this->findOneNode($nodeId, $currentSiteDefaultLanguage, $siteId);

            if ($oldNode) {
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
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     *
     * @return Response
     */
    public function deleteAction($nodeId)
    {
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $nodes = $this->get('open_orchestra_model.repository.node')->findByNodeIdAndSiteId($nodeId, $siteId);
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
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     *
     * @return Response
     */
    public function duplicateAction(Request $request, $nodeId, $version = null)
    {
        $language = $request->get('language');
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        /** @var NodeInterface $node */
        $newNode = $this->get('open_orchestra_backoffice.manager.node')->duplicateNode($nodeId, $siteId, $language, $version);
        $this->dispatchEvent(NodeEvents::NODE_DUPLICATE, new NodeEvent($newNode));

        return array();
    }

    /**
     * @param boolean|null $published
     *
     * @Config\Route("/list/not-published-by-contributor", name="open_orchestra_api_node_list_contributor_not_published", defaults={"published": null})
     * @Config\Route("/list/by-contributor", name="open_orchestra_api_node_list_contributor", defaults={"published": false})
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     *
     * @return FacadeInterface
     */
    public function listNodeByContributor($published)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $content = $this->get('open_orchestra_model.repository.node')->findByContributor($user->getUsername(), $published, 10);

        return $this->get('open_orchestra_api.transformer_manager')->get('node_collection')->transform($content);
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     *
     * @Config\Route("/{nodeId}/list-version", name="open_orchestra_api_node_list_version")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     *
     * @return Response
     */
    public function listVersionAction(Request $request, $nodeId)
    {
        $language = $request->get('language');
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $node = $this->get('open_orchestra_model.repository.node')->findByNodeIdAndLanguageAndSiteId($nodeId, $language, $siteId);

        return $this->get('open_orchestra_api.transformer_manager')->get('node_collection')->transformVersions($node);
    }

    /**
     * @param Request $request
     * @param string  $nodeMongoId
     *
     * @Config\Route("/{nodeMongoId}/update", name="open_orchestra_api_node_update")
     * @Config\Method({"POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
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
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     *
     * @return Response
     */
    public function listStatusesForNodeAction($nodeMongoId)
    {
        $node = $this->get('open_orchestra_model.repository.node')->find($nodeMongoId);

        return $this->listStatuses($node->getStatus());
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     *
     * @Config\Route("/{nodeId}/children/update/order", name="open_orchestra_api_node_update_children_order")
     * @Config\Method({"POST"})
     * @Api\Serialize()
     *
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     *
     * @return Response
     */
    public function updateChildrenOrderAction(Request $request, $nodeId)
    {
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'OpenOrchestra\ApiBundle\Facade\NodeCollectionFacade',
            $request->get('_format', 'json')
        );

        $node = $this->get('open_orchestra_model.repository.node')->findOneByNodeId($nodeId);

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
        $node = $this->get('open_orchestra_model.repository.node')->findOneByNodeIdAndLanguageAndSiteIdAndVersion($nodeId, $language, $siteId, $version);

        return $node;
    }
}
