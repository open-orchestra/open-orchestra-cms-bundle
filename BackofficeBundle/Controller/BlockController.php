<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\ModelInterface\Event\BlockEvent;
use OpenOrchestra\ModelInterface\BlockEvents;
use OpenOrchestra\ModelInterface\BlockNodeEvents;
use OpenOrchestra\ModelInterface\Event\BlockNodeEvent;

/**
 * Class BlockController
 */
class BlockController extends AbstractEditionRoleController
{
    /**
     * @param Request $request
     *
     * @Config\Route("/new/{component}/{nodeId}", name="open_orchestra_backoffice_block_new", defaults={"nodeId" = null})
     * @Config\Method({"GET", "POST"})
     *
     * Config\Security("is_granted('')")
     *
     * @return Response
     */
    public function newAction(Request $request, $component, $nodeId)
    {
        $blockClass = $this->container->getParameter('open_orchestra_model.document.block.class');
        /** @var RedirectionInterface $redirection */
        $block = new $blockClass();
        $block->setComponent($component);
        $block->setTransverse(is_null($nodeId));

        $formType = $this->get('open_orchestra_backoffice.generate_form_manager')->getFormType($block);
        $form = $this->createForm($formType, $block, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_block_new', array(
                'component' => $component,
                'nodeId' => $nodeId,
            )),
            'method' => 'POST',
        ));
        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.block.success');

        if ($this->handleForm($form, $message, $redirection)) {
            $this->dispatchEvent(BlockEvents::POST_BLOCK_CREATE, new BlockEvent($block));
            if (!is_null($nodeId)) {
                $this->dispatchEvent(BlockNodeEvents::ADD_BLOCK_TO_NODE, new BlockNodeEvent($this->get('open_orchestra_model.repository.node')->find($nodeId), $block));
            }
            $response = new Response('', Response::HTTP_CREATED, array('Content-type' => 'text/html; charset=utf-8'));

            return $this->render('BraincraftedBootstrapBundle::flash.html.twig', array(), $response);
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $redirectionId
     *
     * @Config\Route("/form/{redirectionId}/{nodeId}", name="open_orchestra_backoffice_block_form", defaults={"nodeId" = null})
     * @Config\Method({"GET", "POST"})
     *
     * Config\Security("is_granted('')")
     *
     * @return Response
     */
    public function formAction(Request $request, $blockId, $nodeId)
    {
        $block = $this->get('open_orchestra_model.repository.block')->find($blockId);

        $formType = $this->get('open_orchestra_backoffice.generate_form_manager')->getFormType($block);
        $form = $this->createForm($formType, $block, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_block_form', array(
                'blockId' => $blockId
            )),
            'method' => 'POST',
        ));

        $form->handleRequest($request);
        $message =  $this->get('translator')->trans('open_orchestra_backoffice.form.block.edit.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(BlockEvents::POST_BLOCK_UPDATE, new BlockEvent($block));
        }

        return $this->renderAdminForm($form);
    }
}
