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
     * @Api\Serialize()
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
     * @Api\Serialize()
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

        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        $this->dispatchEvent(NodeEvents::NODE_UPDATE_BLOCK_POSITION, new NodeEvent($node));

        return new Response();
    }

    /**
     * @param string $areaId
     * @param string $nodeId
     *
     * @Config\Route("/{areaId}/delete-in-node/{nodeId}", name="open_orchestra_api_area_delete_in_node")
     * @Config\Method({"POST", "DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     *
     * @return Response
     */
    public function deleteAreaFromNodeAction($areaId, $nodeId)
    {
        $node = $this->get('open_orchestra_model.repository.node')->find($nodeId);

        $this->deleteAreaFromContainer($areaId, $node);

        $this->dispatchEvent(NodeEvents::NODE_DELETE_AREA, new NodeEvent($node));

        return new Response();
    }

    /**
     * @param string $areaId
     * @param string $templateId
     *
     * @Config\Route("/{areaId}/delete-in-template/{templateId}", name="open_orchestra_api_area_delete_in_template")
     * @Config\Method({"POST", "DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     *
     * @return Response
     */
    public function deleteAreaFromTemplateAction($areaId, $templateId)
    {
        $areaContainer = $this->get('open_orchestra_model.repository.template')->findOneByTemplateId($templateId);

        $this->deleteAreaFromContainer($areaId, $areaContainer);

        $this->dispatchEvent(TemplateEvents::TEMPLATE_AREA_DELETE, new TemplateEvent($areaContainer));

        return new Response();
    }

    /**
     * @param string $areaId
     * @param string $parentAreaId
     * @param string $nodeId
     * @param string $templateId
     *
     * @Config\Route("/{areaId}/delete-in-area/{parentAreaId}/node/{nodeId}", name="open_orchestra_api_area_delete_in_node_area", defaults={"templateId" = null})
     * @Config\Route("/{areaId}/delete-in-area/{parentAreaId}/template/{templateId}", name="open_orchestra_api_area_delete_in_template_area", defaults={"nodeId" = null})
     * @Config\Method({"POST", "DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     *
     * @return Response
     */
    public function deleteAreaFromAreaAction($areaId, $parentAreaId, $nodeId = null, $templateId = null)
    {
        if ($nodeId) {
            $nodeRepository = $this->get('open_orchestra_model.repository.node');
            $node = $nodeRepository->find($nodeId);
            $areaContainer = $nodeRepository->findAreaByAreaId($node, $parentAreaId);
        }
        if ($templateId && is_null($nodeId)) {
            $areaContainer = $this->get('open_orchestra_model.repository.template')->findAreaByTemplateIdAndAreaId($templateId, $parentAreaId);
        }

        $this->deleteAreaFromContainer($areaId, $areaContainer);

        return new Response();
    }

    /**
     * Remove an area from an areaContainer
     *
     * @param string                 $areaId
     * @param AreaContainerInterface $areaContainer
     */
    protected function deleteAreaFromContainer($areaId, AreaContainerInterface $areaContainer)
    {
        $this->get('open_orchestra_backoffice.manager.area')->deleteAreaFromAreas($areaContainer, $areaId);

        $this->get('doctrine.odm.mongodb.document_manager')->flush();
    }
}
