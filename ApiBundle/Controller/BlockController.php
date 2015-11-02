<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class BlockController
 *
 * @Config\Route("block")
 *
 * @Api\Serialize()
 */
class BlockController extends BaseController
{
    /**
     * @param string $language
     *
     * @Config\Route("/list/{language}", name="open_orchestra_api_block_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_NODE') or is_granted('ROLE_ACCESS_UPDATE_GENERAL_NODE')")
     *
     * @return FacadeInterface
     */
    public function listBlockAction($language)
    {
        $currentSiteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $currentSite = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($currentSiteId);

        $blocks = array();
        if ($currentSite) {
            $blocks = $currentSite->getBlocks();
            if (count($blocks) == 0) {
                $blocks = $this->getParameter('open_orchestra.blocks');
            }
        }
        foreach ($blocks as $key => $block) {
            $blockClass = $this->container->getParameter('open_orchestra_model.document.block.class');
            $blocks[$key] = new $blockClass();
            $blocks[$key]->setComponent($block);
        }

        $node = $this->get('open_orchestra_model.repository.node')
            ->findInLastVersion(
                NodeInterface::TRANSVERSE_NODE_ID,
                $language,
                $currentSiteId
            );

        return $this->get('open_orchestra_api.transformer_manager')->get('block_collection')->transform($node->getBlocks(), $blocks, $node->getNodeId());
    }

}
