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
     * @param string $nodeId
     *
     * @Config\Route("/{areaId}/show-in-node/{nodeId}", name="open_orchestra_api_area_show_in_node")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function showInNodeAction($areaId, $nodeId)
    {
        $nodeRepository = $this->get('open_orchestra_model.repository.node');
        $node = $nodeRepository->find($nodeId);
        $this->denyAccessUnlessGranted($this->getAccessRole($node), $node);
        $area = $nodeRepository->findAreaByAreaId($node, $areaId);

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
     */
    public function showInTemplateAction($areaId, $templateId)
    {
        $templateRepository = $this->get('open_orchestra_model.repository.template');
        $template = $templateRepository->findOneByTemplateId($templateId);
        $area = $templateRepository->findAreaByTemplateIdAndAreaId($templateId, $areaId);

        return $this->get('open_orchestra_api.transformer_manager')->get('area')->transformFromTemplate($area, $template);
    }

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
     * @param string     $nodeId
     * @param AreaFacade $facade
     */
    protected function updateArea($nodeId, AreaFacade $facade)
    {
        $nodeRepository = $this->get('open_orchestra_model.repository.node');
        $node = $nodeRepository->find($nodeId);
        $this->denyAccessUnlessGranted($this->getAccessRole($node), $node);

        $area = $nodeRepository->findAreaByAreaId($node, $facade->areaId);

        $this->get('open_orchestra_api.transformer_manager')
            ->get('area')->reverseTransform($facade, $area, $node);

        $this->get('object_manager')->flush();

        $this->dispatchEvent(NodeEvents::NODE_UPDATE_BLOCK_POSITION, new NodeEvent($node));
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param int     $areaFromId
     * @param int     $areaToId
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
     * @param string      $areaId
     * @param string|null $nodeId
     *
     * @Config\Route("/{areaId}/delete-in-node/{nodeId}", name="open_orchestra_api_area_delete_in_node")
     * @Config\Method({"POST", "DELETE"})
     *
     * @return Response
     */
    public function deleteAreaInNodeAction($areaId, $nodeId)
    {
        $node = $this->get('open_orchestra_model.repository.node')->find($nodeId);
        $this->denyAccessUnlessGranted($this->getEditionRole($node), $node);
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
     */
    public function deleteAreaInNodeAreaAction($areaId, $parentAreaId, $nodeId)
    {
        $nodeRepository= $this->get('open_orchestra_model.repository.node');
        $node = $nodeRepository->find($nodeId);
        $this->denyAccessUnlessGranted($this->getEditionRole($node), $node);
        $areaContainer = $nodeRepository->findAreaByAreaId($node, $parentAreaId);
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
     *
     * @return Response
     */
    public function deleteAreaInTemplateAction($areaId, $templateId)
    {
        $areaContainer = $this->get('open_orchestra_model.repository.template')->findOneByTemplateId($templateId);
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
     */
    public function deleteAreaInTemplateAreaAction($areaId, $parentAreaId, $templateId)
    {
        $templateRepository = $this->get('open_orchestra_model.repository.template');
        $template = $templateRepository->findOneByTemplateId($templateId);
        $areaContainer = $templateRepository->findAreaByTemplateIdAndAreaId($templateId, $parentAreaId);
        $this->dispatchEvent(TemplateEvents::TEMPLATE_AREA_DELETE, new TemplateEvent($template));
        $this->deleteAreaFromContainer($areaId, $areaContainer);

        return array();
    }

    /**
     * Remove an area from an areaContainer
     *
     * @param string                 $areaId
     * @param AreaContainerInterface $areaContainer
     */
    protected function deleteAreaFromContainer($areaId, AreaContainerInterface $areaContainer)
    {
        $this->get('open_orchestra_backoffice.manager.area')->deleteAreaFromContainer($areaContainer, $areaId);
        $this->get('object_manager')->flush();
    }

    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    protected function getAccessRole(NodeInterface $node)
    {
        if (NodeInterface::TYPE_ERROR === $node->getNodeType()) {
            return TreeNodesPanelStrategy::ROLE_ACCESS_ERROR_NODE;
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
        if (NodeInterface::TYPE_ERROR === $node->getNodeType()) {
            return TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_ERROR_NODE;
        }

        return TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE;
    }
}
