<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
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
     * @Config\Route("/list/with-transverse/{language}", name="open_orchestra_api_block_list_with_transverse")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_NODE') or is_granted('ROLE_ACCESS_UPDATE_GENERAL_NODE') or is_granted('ROLE_ACCESS_UPDATE_ERROR_NODE')")
     *
     * @return FacadeInterface
     */
    public function listBlockWithTransverseAction($language)
    {
        return $this->listBlock($language, true);
    }

    /**
     * @param string $language
     *
     * @Config\Route("/list/without-transverse/{language}", name="open_orchestra_api_block_list_without_transverse")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_NODE') or is_granted('ROLE_ACCESS_UPDATE_GENERAL_NODE') or is_granted('ROLE_ACCESS_UPDATE_ERROR_NODE')")
     *
     * @return FacadeInterface
     */
    public function listBlockWithoutTransverseAction($language)
    {
        return $this->listBlock($language, false);
    }

    /**
     * @param string $language
     * @param bool   $withTransverseBlocks
     *
     * @return FacadeInterface
     */
    protected function listBlock($language, $withTransverseBlocks)
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


        $transverseBlocks = array();
        if ($withTransverseBlocks) {
            $transverseBlocks = $this->get('open_orchestra_model.repository.block')->findTransverse();
        }

        return $this->get('open_orchestra_api.transformer_manager')->get('block_collection')->transform(
            $transverseBlocks,
            $blocks
        );
    }
}
