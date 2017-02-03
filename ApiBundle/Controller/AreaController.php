<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

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
     *
     * @Config\Route("/{areaId}/show-in-node/{siteId}/{nodeId}/{version}/{language}", name="open_orchestra_api_area_show_in_node")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function showAreaNodeAction($areaId, $nodeId, $language, $version, $siteId)
    {
        $node = $this->get('open_orchestra_model.repository.node')->findVersionNotDeleted($nodeId, $language, $siteId, $version);
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, $node);

        $area = $this->get('open_orchestra_model.repository.node')->findAreaInNodeByAreaId($node, $areaId);

        return $this->get('open_orchestra_api.transformer_manager')->get('area')->transform($areaId, $area, $node);
    }
}
