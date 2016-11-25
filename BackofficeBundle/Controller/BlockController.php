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
class BlockController extends AbstractEditionRoleController
{
    /**
     * @param Request $request
     * @param string  $nodeId
     * @param int     $blockNumber
     *
     * @Config\Route("/block/form/{nodeId}/{blockNumber}", name="open_orchestra_backoffice_block_form", requirements={"blockNumber" = "\d+"}, defaults={"blockNumber" = 0})
     * @Config\Method({"GET", "POST", "PATCH"})
     *
     * @return Response
     */
    public function formAction(Request $request, $nodeId, $blockNumber = 0)
    {
        $node = $this->get('open_orchestra_model.repository.node')->find($nodeId);
        $this->denyAccessUnlessGranted($this->getAccessRole($node), $node);
        $block = $node->getBlocks()->get($blockNumber);

        $options = array(
            'action' => $this->generateUrl('open_orchestra_backoffice_block_form', array(
                'nodeId' => $nodeId,
                'blockNumber' => $blockNumber)),
            'blockPosition' => $blockNumber,
        );

        if ($node) {
            $options['disabled'] = !$this->get('security.authorization_checker')->isGranted($this->getEditionRole($node), $node);
        }

        $options['method'] = 'POST';
        if ('PATCH' === $request->getMethod()) {
            $options['validation_groups'] = false;
            $options['method'] = 'PATCH';
        }

        $formType = $this->get('open_orchestra_backoffice.generate_form_manager')->getFormType($block);
        $form = $this->createForm($formType, $block, $options);
        $form->handleRequest($request);

        if ('PATCH' !== $request->getMethod()) {
            $message = $this->get('translator')->trans('open_orchestra_backoffice.form.block.success');
            if ($this->handleForm($form, $message)) {
                $this->dispatchEvent(NodeEvents::NODE_UPDATE_BLOCK, new NodeEvent($node), null, $block);
            }
        }

        $title = 'open_orchestra_backoffice.block.' . $block->getComponent() . '.title';
        $title = $this->get('translator')->trans($title);

        return $this->renderAdminForm(
            $form,
            array('title' => $title),
            null,
            $form->getConfig()->getAttribute('template')
        );
    }
}
