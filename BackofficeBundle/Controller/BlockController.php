<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\BackofficeBundle\Form\Type\BlockType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BlockController
 */
class BlockController extends Controller
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
        $node = $this->get('php_orchestra_model.repository.node')->findOneByNodeIdAndVersion($nodeId);
        $block = $node->getBlocks()->get($blockNumber);

        $form = $this->createForm(
            'block',
            $block,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_block_form', array(
                    'nodeId' => $nodeId,
                    'blockNumber' => $blockNumber
                ))
            )
        );

        $refresh = false;
        $form->handleRequest($request);
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($node);
            $documentManager->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('php_orchestra_backoffice.form.block.success')
            );
            $refresh = true;
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView(),
            'blockType' => $block->getComponent(),
            'refresh' => $refresh
        ));
    }
}
