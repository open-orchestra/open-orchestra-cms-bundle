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
     * @param int     $blockPosition
     *
     * @Config\Route("{nodeId}/{areaId}/remove-block/{blockPosition}", name="php_orchestra_api_area_remove_block", requirements={"blockPosition" = "\d+"}, defaults={"blockPosition" = 0})
     * @Config\Method({"POST", "DELETE"})
     *
     * @return Response
     */
    public function removeBlockFromAreaAction(Request $request, $nodeId, $areaId, $blockPosition = 0)
    {
        $area = $this->get('php_orchestra_model.repository.node')->findAreaByNodeIdAndAreaId($nodeId, $areaId);

        $area = $this->get('php_orchestra_backoffice.manager.area')->removeBlockFromArea($area, $blockPosition);

        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response();
    }

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
}
