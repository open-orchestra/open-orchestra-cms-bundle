<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Event\TemplateEvent;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Model\AreaContainerInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\ModelInterface\TemplateEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

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
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     *
     * @return FacadeInterface
     */
    public function showInNodeAction($areaId, $nodeId)
    {
        $nodeRepository = $this->get('open_orchestra_model.repository.node');
        $node = $nodeRepository->find($nodeId);
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
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
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
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     *
     * @return Response
     */
    public function updateBlockInAreaAction(Request $request, $nodeId, $areaId)
    {
        $nodeRepository = $this->get('open_orchestra_model.repository.node');
        $node = $nodeRepository->find($nodeId);
        $area = $nodeRepository->findAreaByAreaId($node, $areaId);

        $facade = $this->get('jms_serializer')->deserialize($request->getContent(), 'OpenOrchestra\ApiBundle\Facade\AreaFacade', $request->get('_format', 'json'));

        $this->get('open_orchestra_api.transformer_manager')->get('area')->reverseTransform($facade, $area, $node);

        $this->get('object_manager')->flush();

        $this->dispatchEvent(NodeEvents::NODE_UPDATE_BLOCK_POSITION, new NodeEvent($node));

        return array();
    }

    /**
     * @param string      $areaId
     * @param string|null $nodeId
     *
     * @Config\Route("/{areaId}/delete-in-node/{nodeId}", name="open_orchestra_api_area_delete_in_node")
     * @Config\Method({"POST", "DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     *
     * @return Response
     */
    public function deleteAreaInNodeAction($areaId, $nodeId)
    {
        $areaContainer = $this->get('open_orchestra_model.repository.node')->find($nodeId);
        $this->dispatchEvent(NodeEvents::NODE_DELETE_AREA, new NodeEvent($node));
        $this->deleteAreaFromContainer($areaId, $areaContainer);

        return array();
    }

    /**
     * @param string      $areaId
     * @param string      $parentAreaId
     * @param string|null $nodeId
     *
     * @Config\Route("/{areaId}/delete-in-area/{parentAreaId}/node/{nodeId}", name="open_orchestra_api_area_delete_in_node_area")
     *
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAreaInNodeAreaAction($areaId, $parentAreaId, $nodeId)
    {
        $nodeRepository= $this->get('open_orchestra_model.repository.node');
        $node = $nodeRepository->find($nodeId);
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
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
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
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
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
}
