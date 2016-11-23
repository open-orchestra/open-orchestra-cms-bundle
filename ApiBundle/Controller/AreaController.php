<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
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
        $node = $this->get('open_orchestra_model.repository.node')->findVersion($nodeId, $language, $siteId, $version);
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, $node);

        $area = $this->get('open_orchestra_model.repository.node')->findAreaInNodeByAreaId($node, $areaId);

        return $this->get('open_orchestra_api.transformer_manager')->get('area')->transform($areaId, $area, $node);
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param string  $language
     * @param string  $version
     * @param string  $siteId
     *
     * @Config\Route("/{siteId}/{nodeId}/{version}/{language}/delete-block", name="open_orchestra_api_area_delete_block")
     * @Config\Method({"POST"})
     *
     * @return FacadeInterface
     */
    public function deleteBlockAction(Request $request, $nodeId, $language, $version, $siteId) {
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'OpenOrchestra\ApiBundle\Facade\AreaCollectionFacade',
            $request->get('_format', 'json')
        );
        $node = $this->get('open_orchestra_model.repository.node')->findVersion($nodeId, $language, $siteId, $version);
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $node);

        $areaClass = $this->container->getParameter('open_orchestra_model.document.area.class');

        foreach ($facade->getAreas() as $key => $facadeArea) {
            $deletedBlocks = array();
            $area = $node->getArea($key);
            if ($area instanceof AreaInterface){
                foreach ($node->getArea($key)->getBlocks() as $block) {
                    $deletedBlocks[$block->getId()] = $block;
                }
            }
            $blocks = array();
            foreach ($facadeArea->getBlocks() as $block) {
                if (array_key_exists($block->getId(), $deletedBlocks)) {
                    unset($deletedBlocks[$block->getId()]);
                }
                $blocks[] = $this->get('open_orchestra_model.repository.block')->findOne($block->getId());
            }
            $area = new $areaClass();
            $area->setBlocks($blocks);
            $node->setArea($key, $area);
            foreach($deletedBlocks as $block) {
                $this->get('object_manager')->remove($block);
            }
        }

        $this->get('object_manager')->flush();

        $this->dispatchEvent(NodeEvents::NODE_DELETE_BLOCK, new NodeEvent($node));
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param string  $language
     * @param string  $version
     * @param string  $siteId
     *
     * @Config\Route("/{siteId}/{nodeId}/{version}/{language}/update-block-position", name="open_orchestra_api_area_update_block_position")
     * @Config\Method({"POST"})
     *
     * @return FacadeInterface
     */
    public function updateBlockPositionAction(Request $request, $nodeId, $language, $version, $siteId) {
        $areaClass = $this->container->getParameter('open_orchestra_model.document.area.class');
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'OpenOrchestra\ApiBundle\Facade\AreaCollectionFacade',
            $request->get('_format', 'json')
        );
        $node = $this->get('open_orchestra_model.repository.node')->findVersion($nodeId, $language, $siteId, $version);
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $node);

        foreach ($facade->getAreas() as $key => $facadeArea) {
            $blocks = array();
            foreach ($facadeArea->getBlocks() as $block) {
                $blocks[] = $this->get('open_orchestra_model.repository.block')->findOne($block->getId());
            }
            $area = new $areaClass();
            $area->setBlocks($blocks);
            $node->setArea($key, $area);
        }

        $this->get('object_manager')->flush();

        $this->dispatchEvent(NodeEvents::NODE_UPDATE_BLOCK_POSITION, new NodeEvent($node));
    }
}
