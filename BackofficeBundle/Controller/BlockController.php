<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\BackofficeBundle\Form\Type\BlockType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPOrchestra\ModelBundle\Model\NodeInterface;
use PHPOrchestra\ModelBundle\Model\BlockInterface;

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
     * @Config\Route("/block/form/{nodeId}/{blockNumber}", name="php_orchestra_backoffice_block_form", requirements={"blockNumber" = "\d+"}, defaults={"blockNumber" = 0})
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $nodeId, $blockNumber = 0)
    {
        $node = $this->get('php_orchestra_model.repository.node')->find($nodeId);
        $block = $node->getBlocks()->get($blockNumber);

        $form = $this->createForm(
            'block',
            $block,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_block_form', array(
                    'nodeId' => $nodeId,
                    'blockNumber' => $blockNumber
                )),
                'blockPosition' => $blockNumber
            )
        );

        $form->handleRequest($request);

        $this->handleForm($form, $this->get('translator')
            ->trans('php_orchestra_backoffice.form.block.success'), $node);

        return $this->renderAdminForm($form, array('blockType' => $block->getComponent()));
    }

    /**
     * List all possible blocks
     *
     * @return Response
     */
    public function listPossibleBlocksAction()
    {
        $blocks = $this->container->getParameter('php_orchestra.blocks');

        return $this->render('PHPOrchestraBackofficeBundle:Block:possibleBlocksList.html.twig', array(
            'blocks' => $blocks
        ));
    }

    /**
     * @param string $language
     *
     * @Config\Route("/block/existing/{language}", name="php_orchestra_backoffice_block_exsting")
     * @Config\Method({"GET"})
     *
     * @return Response
     */
    public function listExistingBlocksAction($language)
    {
        $node = $this->get('php_orchestra_model.repository.node')
            ->findOneByNodeIdAndLanguageAndSiteIdAndLastVersion(NodeInterface::TRANSVERSE_NODE_ID, $language);
        if ($node) {
            $blocksFacade = array();
            $transformer = $this->get('php_orchestra_api.transformer_manager')->get('block');
            $blocks = $node->getBlocks();
            foreach ($blocks as $key => $block) {
                $blocksFacade[$key] = $transformer->transform($block, false);
            }

            return $this->render('PHPOrchestraBackofficeBundle:Block:existingBlocksList.html.twig', array(
                'blocks' => $blocksFacade,
                'nodeId' => $node->getNodeId()
            ));
        }

        return new Response('');
    }
}
