<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\NodeEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class BlockController
 */
class BlockController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $nodeId
     * @param int     $blockNumber
     *
     * @Config\Route("/block/form/{nodeId}/{blockNumber}", name="open_orchestra_backoffice_block_form", requirements={"blockNumber" = "\d+"}, defaults={"blockNumber" = 0})
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     *
     * @return Response
     */
    public function formAction(Request $request, $nodeId, $blockNumber = 0)
    {
        $node = $this->get('open_orchestra_model.repository.node')->find($nodeId);
        $block = $node->getBlocks()->get($blockNumber);
        $form = $this->createForm(
            'block',
            $block,
            array(
                'action' => $this->generateUrl('open_orchestra_backoffice_block_form', array(
                    'nodeId' => $nodeId,
                    'blockNumber' => $blockNumber
                )),
                'blockPosition' => $blockNumber,
                'disabled' => !$node->isEditable()
            )
        );

        $form->handleRequest($request);

        $this->handleForm($form, $this->get('translator')
            ->trans('open_orchestra_backoffice.form.block.success'), $node);

        $this->dispatchEvent(NodeEvents::NODE_UPDATE_BLOCK, new NodeEvent($node));
        return $this->renderAdminForm(
            $form,
            array('blockType' => $block->getComponent()),
            null,
            $form->getConfig()->getAttribute('template')
        );
    }

    /**
     * List all possible blocks
     *
     * @return Response
     */
    public function listPossibleBlocksAction()
    {
        $blocks = array();

        $currentSiteId = $this->container->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        if ($currentSiteId) {
            $currentSite = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($currentSiteId);
            if ($currentSite) {
                $blocks = $currentSite->getBlocks();
                if (count($blocks) == 0) {
                    $blocks = $this->container->getParameter('open_orchestra.blocks');
                }
            }
        }

        return $this->render('OpenOrchestraBackofficeBundle:Block:possibleBlocksList.html.twig', array(
            'blocks' => $blocks
        ));
    }

    /**
     * @param string $language
     *
     * @Config\Route("/block/existing/{language}", name="open_orchestra_backoffice_block_exsting")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_TREE_NODE')")
     *
     * @return Response
     */
    public function listExistingBlocksAction($language)
    {
        $node = $this->get('open_orchestra_model.repository.node')
            ->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion(NodeInterface::TRANSVERSE_NODE_ID, $language);
        if ($node) {
            $blocksFacade = array();
            $transformer = $this->get('open_orchestra_api.transformer_manager')->get('block');
            $blocks = $node->getBlocks();
            foreach ($blocks as $key => $block) {
                $blocksFacade[$key] = $transformer->transform($block, false);
            }

            return $this->render('OpenOrchestraBackofficeBundle:Block:existingBlocksList.html.twig', array(
                'blocks' => $blocksFacade,
                'nodeId' => $node->getNodeId()
            ));
        }

        return new Response('');
    }
}
