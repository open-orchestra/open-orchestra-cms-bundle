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
    /**
     * @param string $areaId
     * @param string  $nodeId
     * @param string  $language
     * @param string  $version
     * @param string  $siteId
     * @param string  $areaParentId
     *
     * @Config\Route("/{areaId}/show-in-node/{siteId}/{nodeId}/{version}/{language}/{areaParentId}", name="open_orchestra_api_area_show_in_node")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function showAreaNodeAction($areaId, $nodeId, $language, $version, $siteId, $areaParentId)
    {
        $node = $this->get('open_orchestra_model.repository.node')->findVersion($nodeId, $language, $siteId, $version);
        $this->denyAccessUnlessGranted($this->getAccessRole($node), $node);

        $rootArea = $node->getRootArea();
        $area = $this->get('open_orchestra_model.repository.node')->findAreaByAreaId($rootArea, $areaId);

        return $this->get('open_orchestra_api.transformer_manager')->get('area')->transform($area, $node, $areaParentId);
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param string  $language
     * @param string  $version
     * @param string  $siteId
     *
     * @Config\Route("/{siteId}/{nodeId}/{version}/{language}/update-block", name="open_orchestra_api_area_update_block")
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function updateBlockAreaAction(Request $request, $nodeId, $language, $version, $siteId)
    {
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'OpenOrchestra\ApiBundle\Facade\AreaFacade',
            $request->get('_format', 'json')
        );

        $this->updateArea($facade, $nodeId, $language, $version, $siteId);

        return array();
    }


    /**
     * @param Request $request
     * @param string  $nodeId
     * @param string  $language
     * @param string  $version
     * @param string  $siteId
     *
     * @Config\Route("/{siteId}/{nodeId}/{version}/{language}/move-block", name="open_orchestra_api_area_move_block")
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function moveBlockFromAreaToAreaAction(Request $request, $nodeId, $language, $version, $siteId)
    {
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'OpenOrchestra\ApiBundle\Facade\AreaCollectionFacade',
            $request->get('_format', 'json')
        );
        $areas = $facade->getAreas();

        $this->updateArea($areas[0], $nodeId, $language, $version, $siteId);
        $this->updateArea($areas[1], $nodeId, $language, $version, $siteId);

        $this->get('object_manager')->flush();

        return array();
    }

    /**
     * @param AreaFacade $facade
     * @param string     $nodeId
     * @param string     $language
     * @param string     $version
     * @param string     $siteId
     */
    protected function updateArea(AreaFacade $facade, $nodeId, $language, $version, $siteId)
    {
        $node = $this->get('open_orchestra_model.repository.node')->findVersion($nodeId, $language, $siteId, $version);
        $this->denyAccessUnlessGranted($this->getEditionRole($node), $node);
        $rootArea = $node->getRootArea();
        $area = $this->get('open_orchestra_model.repository.node')->findAreaByAreaId($rootArea, $facade->areaId);

        $this->get('open_orchestra_api.transformer_manager')
            ->get('area')->reverseTransform($facade, $area, $node);

        $this->dispatchEvent(NodeEvents::NODE_UPDATE_BLOCK_POSITION, new NodeEvent($node));
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
    public function moveAreaTemplateAction(Request $request, $areaParentId, $templateId)
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
    public function moveAreaNodeAction(Request $request, $areaParentId, $nodeId, $language, $version, $siteId)
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

        $rootArea = $template->getRootArea();
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

        $rootArea = $node->getRootArea();
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
     * @param NodeInterface $node
     *
     * @return string
     *
     */
    protected function getAccessRole(NodeInterface $node)
    {
        if (NodeInterface::TYPE_ERROR === $node->getNodeType()) {
            return TreeNodesPanelStrategy::ROLE_ACCESS_ERROR_NODE;
        }

        return TreeNodesPanelStrategy::ROLE_ACCESS_TREE_NODE;
    }
}
