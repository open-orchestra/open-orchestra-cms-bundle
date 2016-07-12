<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Event\TemplateEvent;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Model\AreaContainerInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\ModelInterface\TemplateEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\ApiBundle\Facade\AreaFacade;

/**
 * Class AreaController
 *
 * @Config\Route("area")
 *
 * @Api\Serialize
 */
class AreaController extends BaseController
{
    /** @deprecated  will be removed in 2.0*/
    const ROLE_ACCESS = 'access';
    /** @deprecated  will be removed in 2.0*/
    const ROLE_EDIT = 'edit';

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param int     $areaId
     *
     * @Config\Route("/{nodeId}/{areaId}/update-block", name="open_orchestra_api_area_update_block")
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function updateBlockInAreaAction(Request $request, $nodeId, $areaId)
    {
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'OpenOrchestra\ApiBundle\Facade\AreaFacade',
            $request->get('_format', 'json')
        );

        $this->updateArea($nodeId, $facade);

        return array();
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     *
     * @Config\Route("/move-block/{nodeId}", name="open_orchestra_api_area_move_block")
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function moveBlockFromAreaToAreaAction(Request $request, $nodeId)
    {
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'OpenOrchestra\ApiBundle\Facade\AreaCollectionFacade',
            $request->get('_format', 'json')
        );

        $areas = $facade->getAreas();

        $this->updateArea($nodeId,$areas[1]);
        $this->updateArea($nodeId, $areas[0]);

        return array();
    }

    /**
     * @param Request $request
     * @param string  $areaParentId
     * @param string  $templateId
     *
     * @Config\Route("/{areaParentId}/{templateId}/move_area", name="open_orchestra_api_area_move_in_template")
     * @Config\Method({"POST"})
     *
     * @Config\Security("is_granted('ROLE_UPDATE_TREE_TEMPLATE')")
     *
     * @return Response
     */
    public function moveAreaInTemplateAction(Request $request, $areaParentId, $templateId)
    {
        $areaFacade = $this->get('jms_serializer')->deserialize($request->getContent(), 'OpenOrchestra\ApiBundle\Facade\AreaFacade', $request->get('_format', 'json'));

        $template = $this->get('open_orchestra_model.repository.template')->findOneByTemplateId($templateId);
        $areaParent = $this->get('open_orchestra_model.repository.template')->findAreaInTemplateByAreaId($template, $areaParentId);

        $this->get('open_orchestra_api.transformer.area')->reverseTransform($areaFacade, $areaParent);
        $this->get('object_manager')->flush();

        return array();
    }

    /**
     * @param Request $request
     * @param string  $areaParentId
     * @param string  $nodeId
     * @param string  $language
     * @param string  $version
     * @param string  $siteId
     *
     * @Config\Route("/{areaParentId}/{siteId}/{nodeId}/{version}/{language}/move_area", name="open_orchestra_api_area_move_in_node")
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function moveAreaInNodeAction(Request $request, $areaParentId, $nodeId, $language, $version, $siteId)
    {
        $areaFacade = $this->get('jms_serializer')->deserialize($request->getContent(), 'OpenOrchestra\ApiBundle\Facade\AreaFacade', $request->get('_format', 'json'));

        $node = $this->get('open_orchestra_model.repository.node')->findVersion($nodeId, $language, $siteId, $version);
        $this->denyAccessUnlessGranted($this->getEditionRole($node), $node);

        $areaParent = $this->get('open_orchestra_model.repository.node')->findAreaInNodeByAreaId($node, $areaParentId);

        $this->get('open_orchestra_api.transformer.area')->reverseTransform($areaFacade, $areaParent);
        $this->get('object_manager')->flush();

        return array();
    }

    /**
     * @param string      $areaId
     * @param string      $templateId
     * @param string|null $areaParentId
     *
     * @Config\Route("/{areaId}/delete-column-in-template/{templateId}/{areaParentId}", name="open_orchestra_api_area_column_delete_in_template")
     * @Config\Route("/{areaId}/delete-row-in-template/{templateId}", name="open_orchestra_api_area_row_delete_in_template")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_GENERAL_NODE')")
     *
     * @return Response
     */
    public function deleteAreaTemplateAction($areaId, $templateId, $areaParentId = null)
    {
        $template = $this->get('open_orchestra_model.repository.template')->findOneByTemplateId($templateId);

        $rootArea = $template->getArea();
        $removedArea = $areaId;
        if (null !== $areaParentId) {
            $parentArea = $this->get('open_orchestra_model.repository.template')->findAreaInTemplateByAreaId($template, $areaParentId);
            if (1 === count($parentArea->getAreas())) {
                $removedArea = $parentArea->getAreaId();
            }
        }
        if (null !== $rootArea) {
            $rootArea->removeAreaByAreaId($removedArea);
            $this->dispatchEvent(TemplateEvents::TEMPLATE_AREA_UPDATE, new TemplateEvent($template));
            $this->get('object_manager')->flush();
        }

        return array();
    }

    /**
     * @param string      $areaId
     * @param string      $nodeId
     * @param string      $language
     * @param string      $version
     * @param string      $siteId
     * @param string|null $areaParentId
     *
     * @Config\Route("/{areaId}/delete-column-in-node/{siteId}/{nodeId}/{version}/{language}/{areaParentId}", name="open_orchestra_api_area_column_delete_in_node")
     * @Config\Route("/{areaId}/delete-row-in-node/{siteId}/{nodeId}/{version}/{language}", name="open_orchestra_api_area_row_delete_in_node")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAreaNodeAction($areaId, $nodeId, $language, $version, $siteId, $areaParentId = null)
    {
        $node = $this->get('open_orchestra_model.repository.node')->findVersion($nodeId, $language, $siteId, $version);
        $this->denyAccessUnlessGranted($this->getEditionRole($node), $node);

        $rootArea = $node->getArea();
        $removedArea = $areaId;
        if (null !== $areaParentId) {
            $parentArea = $this->get('open_orchestra_model.repository.node')->findAreaInNodeByAreaId($node, $areaParentId);
            if (1 === count($parentArea->getAreas())) {
                $removedArea = $parentArea->getAreaId();
            }
        }
        if (null !== $rootArea) {
            $rootArea->removeAreaByAreaId($removedArea);
            $this->dispatchEvent(NodeEvents::NODE_DELETE_AREA, new NodeEvent($node));
            $this->get('object_manager')->flush();
        }

        return array();
    }


    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    protected function getEditionRole(NodeInterface $node)
    {
        if (NodeInterface::TYPE_ERROR === $node->getNodeType()) {
            return TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_ERROR_NODE;
        }

        return TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE;
    }

    /**
     * @param string $areaId
     * @param string $nodeId
     *
     * @Config\Route("/{areaId}/show-in-node/{nodeId}", name="open_orchestra_api_area_show_in_node")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     *
     * @deprecated will be removed in 2.0
     */
    public function showInNodeAction($areaId, $nodeId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $node = $this->getNode($nodeId, self::ROLE_ACCESS);
        $area = $this->get('open_orchestra_model.repository.node')->findAreaByAreaId($node, $areaId);

        return $this->get('open_orchestra_api.transformer_manager')->get('area')->transform($area, $node);
    }

    /**
     * @param string $areaId
     * @param string $templateId
     *
     * @Config\Route("/{areaId}/show-in-template/{templateId}", name="open_orchestra_api_area_show_in_template")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_TREE_GENERAL_NODE')")
     *
     * @return FacadeInterface
     *
     * @deprecated will be removed in 2.0
     */
    public function showInTemplateAction($areaId, $templateId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $templateRepository = $this->get('open_orchestra_model.repository.template');
        $template = $templateRepository->findOneByTemplateId($templateId);
        $area = $templateRepository->findAreaByTemplateIdAndAreaId($templateId, $areaId);

        return $this->get('open_orchestra_api.transformer_manager')
            ->get('area')->transformFromTemplate($area, $template);
    }


    /**
     * @param string      $areaId
     * @param string|null $nodeId
     *
     * @Config\Route("/{areaId}/delete-in-node/{nodeId}", name="open_orchestra_api_area_delete_in_node")
     * @Config\Method({"POST", "DELETE"})
     *
     * @return Response
     * @deprecated will be removed in 2.0
     */
    public function deleteAreaInNodeAction($areaId, $nodeId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $node = $this->getNode($nodeId, self::ROLE_EDIT);
        $this->dispatchEvent(NodeEvents::NODE_DELETE_AREA, new NodeEvent($node));
        $this->deleteAreaFromContainer($areaId, $node);

        return array();
    }

    /**
     * @param string      $areaId
     * @param string      $parentAreaId
     * @param string|null $nodeId
     *
     * @Config\Route("/{areaId}/delete-in-area/{parentAreaId}/node/{nodeId}", name="open_orchestra_api_area_delete_in_node_area")
     *
     * @Config\Method({"DELETE"})
     *
     * @return Response
     * @deprecated will be removed in 2.0
     */
    public function deleteAreaInNodeAreaAction($areaId, $parentAreaId, $nodeId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $node = $this->getNode($nodeId, self::ROLE_EDIT);
        $areaContainer = $this->get('open_orchestra_model.repository.node')
            ->findAreaByAreaId($node, $parentAreaId);
        $this->dispatchEvent(NodeEvents::NODE_DELETE_AREA, new NodeEvent($node));
        $this->deleteAreaFromContainer($areaId, $areaContainer);

        return array();
    }

    /**
     * @param string      $areaId
     * @param string|null $templateId
     *
     * @Config\Route("/{areaId}/delete-in-template/{templateId}", name="open_orchestra_api_area_delete_in_template")
     * @Config\Method({"POST", "DELETE"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_GENERAL_NODE')")

     * @return Response
     * @deprecated will be removed in 2.0
     */
    public function deleteAreaInTemplateAction($areaId, $templateId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $areaContainer = $this->get('open_orchestra_model.repository.template')
            ->findOneByTemplateId($templateId);
        $this->dispatchEvent(TemplateEvents::TEMPLATE_AREA_DELETE, new TemplateEvent($areaContainer));
        $this->deleteAreaFromContainer($areaId, $areaContainer);

        return array();
    }

    /**
     * @param string      $areaId
     * @param string      $parentAreaId
     * @param string|null $templateId
     *
     * @Config\Route("/{areaId}/delete-in-area/{parentAreaId}/template/{templateId}", name="open_orchestra_api_area_delete_in_template_area")
     * @Config\Method({"POST", "DELETE"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_GENERAL_NODE')")
     *
     * @return Response
     * @deprecated will be removed in 2.0
     */
    public function deleteAreaInTemplateAreaAction($areaId, $parentAreaId, $templateId)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $templateRepository = $this->get('open_orchestra_model.repository.template');
        $template = $templateRepository->findOneByTemplateId($templateId);
        $areaContainer = $templateRepository->findAreaByTemplateIdAndAreaId($templateId, $parentAreaId);
        $this->dispatchEvent(TemplateEvents::TEMPLATE_AREA_DELETE, new TemplateEvent($template));
        $this->deleteAreaFromContainer($areaId, $areaContainer);

        return array();
    }

    /**
     * @param string     $nodeId
     * @param AreaFacade $facade
     */
    protected function updateArea($nodeId, AreaFacade $facade)
    {
        $node = $this->getNode($nodeId, self::ROLE_ACCESS);
        $area = $this->get('open_orchestra_model.repository.node')->findAreaByAreaId($node, $facade->areaId);

        $this->get('open_orchestra_api.transformer_manager')
            ->get('area')->reverseTransform($facade, $area, $node);

        $this->get('object_manager')->flush();

        $this->dispatchEvent(NodeEvents::NODE_UPDATE_BLOCK_POSITION, new NodeEvent($node));
    }

    /**
     * Remove an area from an areaContainer
     *
     * @param string                 $areaId
     * @param AreaContainerInterface $areaContainer
     *
     * @deprecated will be removed in 2.0
     */
    protected function deleteAreaFromContainer($areaId, AreaContainerInterface $areaContainer)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $this->get('open_orchestra_backoffice.manager.area')->deleteAreaFromContainer($areaContainer, $areaId);
        $this->get('object_manager')->flush();
    }

    /**
     * @param string $nodeId
     *
     * @return NodeInterface
     *
     * @deprecated will be removed in 2.0
     */
    protected function getNode($nodeId, $roleToCheck)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        $nodeRepository = $this->get('open_orchestra_model.repository.node');
        $node = $nodeRepository->find($nodeId);
        $role = '';
        if (self::ROLE_ACCESS == $roleToCheck) {
            $role = $this->getAccessRole($node);
        } else if (self::ROLE_EDIT) {
            $role = $this->getEditionRole($node);
        }

        $this->denyAccessUnlessGranted($role, $node);

        return $node;
    }

    /**
     * @param NodeInterface $node
     *
     * @return string
     *
     * @deprecated will be removed in 2.0
     *
     */
    protected function getAccessRole(NodeInterface $node)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.2.0 and will be removed in 2.0.', E_USER_DEPRECATED);

        if (NodeInterface::TYPE_ERROR === $node->getNodeType()) {
            return TreeNodesPanelStrategy::ROLE_ACCESS_ERROR_NODE;
        }

        return TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE;
    }
}
