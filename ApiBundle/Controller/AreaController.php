<?php

namespace PHPOrchestra\ApiBundle\Controller;

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
     * @Config\Route("{nodeId}/{areaId}/update-block", name="php_orchestra_api_area_update_block")
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function updateBlockInAreaAction(Request $request, $nodeId, $areaId)
    {
        $nodeRepository = $this->get('php_orchestra_model.repository.node');
        $node = $nodeRepository->findOneByNodeId($nodeId);
        $area = $nodeRepository->findAreaByNodeIdAndAreaId($nodeId, $areaId);

        $facade = $this->get('jms_serializer')->deserialize($request->getContent(), 'PHPOrchestra\ApiBundle\Facade\AreaFacade', $request->get('_format', 'json'));

        $area = $this->get('php_orchestra_api.transformer_manager')->get('area')->reverseTransform($facade, $area, $node);

        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response();
    }

    /**
     * 
     * @param string $areaId
     * @param string $nodeId
     * @param string $parentAreaId
     * 
     * @Config\Route("/{areaId}/delete-in-node/{nodeId}", name="php_orchestra_api_area_delete_in_node", defaults={"parentAreaId" = null})
     * @Config\Route("/{areaId}/delete-in-area/{parentAreaId}/{nodeId}", name="php_orchestra_api_area_delete_in_area")
     * @Config\Method({"POST", "DELETE"})
     *
     * @return Response
     */
    public function deleteAreaAction($areaId, $nodeId, $parentAreaId)
    {
        $areas = null;

        if (is_null($parentAreaId)) {
            $node = $this->get('php_orchestra_model.repository.node')->findOneByNodeId($nodeId);
            $areas = $node->getAreas();
        } else {
            $parentArea = $this->get('php_orchestra_model.repository.node')->findAreaByNodeIdAndAreaId($nodeId, $parentAreaId);
            $areas = $parentArea->getAreas();
        }

        $areas = $this->get('php_orchestra_backoffice.manager.area')->deleteAreaFromAreas($areas, $areaId);

        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response();
    }
}
