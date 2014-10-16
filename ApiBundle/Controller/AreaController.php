<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ModelBundle\Model\AreaContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AreaController
 *
 * @Config\Route("area")
 */
class AreaController extends Controller
{
    /**
     * @param Request $request
     * @param string  $nodeId
     * @param int     $areaId
     *
     * @Config\Route("/{nodeId}/{areaId}/update-block", name="php_orchestra_api_area_update_block")
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function updateBlockInAreaAction(Request $request, $nodeId, $areaId)
    {
        $nodeRepository = $this->get('php_orchestra_model.repository.node');
        $node = $nodeRepository->findOneByNodeIdAndVersion($nodeId);
        $area = $nodeRepository->findAreaByNodeIdAndAreaId($nodeId, $areaId);

        $facade = $this->get('jms_serializer')->deserialize($request->getContent(), 'PHPOrchestra\ApiBundle\Facade\AreaFacade', $request->get('_format', 'json'));

        $area = $this->get('php_orchestra_api.transformer_manager')->get('area')->reverseTransform($facade, $area, $node);

        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response();
    }

    /**
     * @param string $areaId
     * @param string $nodeId
     *
     * @Config\Route("/{areaId}/delete-in-node/{nodeId}", name="php_orchestra_api_area_delete_in_node")
     * @Config\Method({"POST", "DELETE"})
     *
     * @return Response
     */
    public function deleteAreaFromNodeAction($areaId, $nodeId)
    {
        $areaContainer = $this->get('php_orchestra_model.repository.node')->findOneByNodeIdAndSiteIdAndLastVersion(
            $nodeId,
            $this->get('php_orchestra_backoffice.context_manager')->getCurrentSiteId()
        );

        $this->deleteAreaFromContainer($areaId, $areaContainer);

        return new Response();
    }

    /**
     * @param string $areaId
     * @param string $templateId
     *
     * @Config\Route("/{areaId}/delete-in-template/{templateId}", name="php_orchestra_api_area_delete_in_template")
     * @Config\Method({"POST", "DELETE"})
     *
     * @return Response
     */
    public function deleteAreaFromTemplateAction($areaId, $templateId)
    {
        $areaContainer = $this->get('php_orchestra_model.repository.template')->findOneByTemplateId($templateId);

        $this->deleteAreaFromContainer($areaId, $areaContainer);

        return new Response();
    }

    /**
     * @param string $areaId
     * @param string $parentAreaId
     * @param string $nodeId
     * @param string $templateId
     *
     * @Config\Route("/{areaId}/delete-in-area/{parentAreaId}/node/{nodeId}", name="php_orchestra_api_area_delete_in_node_area", defaults={"templateId" = null})
     * @Config\Route("/{areaId}/delete-in-area/{parentAreaId}/template/{templateId}", name="php_orchestra_api_area_delete_in_template_area", defaults={"nodeId" = null})
     * @Config\Method({"POST", "DELETE"})
     *
     * @return Response
     */
    public function deleteAreaFromAreaAction($areaId, $parentAreaId, $nodeId = null, $templateId = null)
    {
        if ($nodeId) {
            $areaContainer = $this->get('php_orchestra_model.repository.node')->findAreaByNodeIdAndAreaId($nodeId, $parentAreaId);
        } else {
            $areaContainer = $this->get('php_orchestra_model.repository.template')->findAreaByTemplateIdAndAreaId($templateId, $parentAreaId);
        }

        $this->deleteAreaFromContainer($areaId, $areaContainer);

        return new Response();
    }

    /**
     * Remove an area from an areaContainer
     *
     * @param string $areaId
     * @param AreaContainerInterface $areaContainer
     */
    protected function deleteAreaFromContainer($areaId, AreaContainerInterface $areaContainer)
    {
        $areas = $this->get('php_orchestra_backoffice.manager.area')->deleteAreaFromAreas($areaContainer, $areaId);

        $this->get('doctrine.odm.mongodb.document_manager')->flush();
    }
}
