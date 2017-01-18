<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\ModelInterface\BlockEvents;
use OpenOrchestra\ModelInterface\Event\BlockEvent;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @param Request $request
     * @param string  $language
     *
     * @return FacadeInterface
     *
     * @Config\Route("/list/shared/{language}", name="open_orchestra_api_block_list_shared_table")
     * @Config\Method({"GET"})
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::BLOCKS_NUMBER_USER
     * })
     */
    public function listSharedBlockTableAction(Request $request, $language)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, BlockInterface::ENTITY_TYPE);
        $mapping = array(
            'label' => 'label',
            'updated_at' => 'updatedAt'
        );
        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);
        $repository = $this->get('open_orchestra_model.repository.block');
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        if ($configuration->getSearchIndex('category') && '' !== $configuration->getSearchIndex('category')) {
            $components = $this->get('open_orchestra_backoffice.manager.block_configuration')->getComponentsWithCategory($configuration->getSearchIndex('category'));
            $configuration->addSearch('components', $components);
        }
        $collection = $repository->findForPaginateBySiteIdAndLanguage($configuration, $siteId, $language, true);
        $recordsTotal = $repository->countBySiteIdAndLanguage($siteId, $language, true);
        $recordsFiltered = $repository->countWithFilterBySiteIdAndLanguage($configuration, $siteId, $language, true);

        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('block_collection');
        $facade = $collectionTransformer->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }

    /**
     * @return FacadeInterface
     *
     * @Config\Route("/list/block-component", name="open_orchestra_api_block_list_block-component")
     * @Config\Method({"GET"})
     */
    public function listAvailableBlockComponentAction()
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, BlockInterface::ENTITY_TYPE);

        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);

        $availableBlocks = $site->getBlocks();

        return $this->get('open_orchestra_api.transformer_manager')->get('block_component_collection')->transform($availableBlocks);
    }

    /**
     * @param string  $blockId
     *
     * @Config\Route("/delete-block/{blockId}", name="open_orchestra_api_block_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteBlockAction($blockId)
    {
        $block = $this->get('open_orchestra_model.repository.block')->findById($blockId);
        if (!$block instanceof BlockInterface) {
            throw new \UnexpectedValueException();
        }

        $this->denyAccessUnlessGranted(ContributionActionInterface::DELETE, $block);
        if (0 !== $this->get('open_orchestra_model.repository.node')->countBlockUsed($block->getId())) {
            $this->createAccessDeniedException();
        }

        $objectManager = $this->get('object_manager');
        $objectManager->remove($block);
        $objectManager->flush();
        $this->dispatchEvent(BlockEvents::POST_BLOCK_DELETE, new BlockEvent($block));

        return array();
    }

    /**
     * @param string $language
     *
     * @Config\Route("/list/with-transverse/{language}", name="open_orchestra_api_block_list_with_transverse")
     * @Config\Method({"GET"})
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
