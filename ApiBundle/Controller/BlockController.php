<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class BlockController
 *
 * @Config\Route("block")
 */
class BlockController extends BaseController
{

    /**
     * @param Request $request
     * @param string $nodeId
     *
     * @Config\Route("/list/{language}", name="open_orchestra_api_block_list")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     *
     * @return FacadeInterface
     */
    public function listBlockAction($language)
    {
        $blocks = array('generateBlocks' => array(), 'loadBlocks' => array());

        $currentSiteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        if ($currentSiteId) {
            $currentSite = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($currentSiteId);
            if ($currentSite) {
                $blocks['generateBlocks'] = $currentSite->getBlocks();
                if (count($blocks) == 0) {
                    $blocks['generateBlocks'] = $this->getParameter('open_orchestra.blocks');
                }
            }
        }
        foreach($blocks['generateBlocks'] as $key => $block){
            $blocks['generateBlocks'][$key] = array('name'  => $block, 'icon' => $this->get('open_orchestra_backoffice.display_icon_manager')->show($block));
        }

        $node = $this->get('open_orchestra_model.repository.node')
        ->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion(NodeInterface::TRANSVERSE_NODE_ID, $language);
        if ($node) {
            $blocksFacade = array();
            $transformer = $this->get('open_orchestra_api.transformer_manager')->get('block');
            $blocks['loadBlocks'] = $node->getBlocks();
            foreach ($blocks['loadBlocks'] as $key => $block) {
                $blocks['loadBlocks'][$key] = $transformer->transform($block, false, $node->getNodeId(), $key);
            }
        }

        return $blocks;
    }

}
