<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\BackofficeBundle\Form\Type\BlockType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;

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

        $form->handleRequest($request);

        $this->handleForm(
            $form,
            $this->get('translator')->trans('php_orchestra_backoffice.form.block.success'),
            $node
        );

        return $this->renderAdminForm($form);
    }
}
