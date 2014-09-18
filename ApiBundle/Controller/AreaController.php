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
     * @Config\Route("/remove-block/{nodeId}/{areaId}/{blockPosition}", name="php_orchestra_api_area_remove_block", requirements={"blockPosition" = "\d+"}, defaults={"blockPosition" = 0})
     * @Config\Method({"POST", "DELETE"})
     *
     * @return Response
     */
    public function removeAction(Request $request, $nodeId, $areaId, $blockPosition = 0)
    {
        $area = $this->get('php_orchestra_model.repository.node')->findAreaByNodeIdAndAreaId($nodeId, $areaId);

        $blocks = $area->getBlocks();
        $blockComponent = $this->getBlockComponent($blocks[$blockPosition], $nodeId);
        unset($blocks[$blockPosition]);

        $area->setBlocks($blocks);
        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans(
                'php_orchestra_backoffice.block.remove.success',
                array(
                    '%blockComponent%' => strtolower(
                        $this->get('translator')->trans('php_orchestra_backoffice.block.' . $blockComponent . '.title')
                    )
                )
            )
        );

        return new Response();
    }

    protected function getBlockComponent($block, $currentNodeId)
    {
        $blockId = $block['blockId'];

        $targetNodeId = $block['nodeId'];
        if (0 == $targetNodeId) {
            $targetNodeId = $currentNodeId;
        }
        $node = $this->get('php_orchestra_model.repository.node')->findOneByNodeId($targetNodeId);

        $blocks = $node->getBlocks();
        return $blocks[$blockId]->getComponent();
    }
}
