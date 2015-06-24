<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\NodeEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.block.success');

        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(NodeEvents::NODE_UPDATE_BLOCK, new NodeEvent($node));
        }

        $title = 'open_orchestra_backoffice.block.'.$block->getComponent().'.title';
        $title = $this->get('translator')->trans($title);

        return $this->renderAdminForm(
            $form,
            array('title' => $title),
            null,
            $form->getConfig()->getAttribute('template')
        );
    }

}
