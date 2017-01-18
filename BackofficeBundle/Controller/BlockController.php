<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\ModelInterface\Event\BlockEvent;
use OpenOrchestra\ModelInterface\BlockEvents;

/**
 * Class BlockController
 */
class BlockController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $component
     * @param string  $language
     *
     * @Config\Route("/block/new/shared/{component}/{language}", name="open_orchestra_backoffice_shared_block_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request, $component, $language)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, BlockInterface::ENTITY_TYPE);
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $blockManager = $this->get('open_orchestra_backoffice.manager.block');

        $block = $blockManager->initializeBlock($component, $siteId, $language, true);

        $formType = $this->get('open_orchestra_backoffice.generate_form_manager')->getFormType($block);
        $form = $this->createForm($formType, $block, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_shared_block_new', array(
                'component' => $component,
                'language' => $language,
            )),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('object_manager');
            $documentManager->persist($block);
            $documentManager->flush();
            $message = $this->get('translator')->trans('open_orchestra_backoffice.form.block.creation');

            $this->dispatchEvent(BlockEvents::POST_BLOCK_CREATE, new BlockEvent($block));
            $response = new Response(
                $message,
                Response::HTTP_CREATED,
                array('Content-type' => 'text/plain; charset=utf-8', 'blockId' => $block->getId(), 'blockLabel' => $block->getLabel())
            );

            return $response;
        }

        return $this->renderAdminForm($form, array(), null, $form->getConfig()->getAttribute('template'));
    }

    /**
     * @param Request $request
     * @param string  $blockId
     *
     * @Config\Route("/block/form/{blockId}", name="open_orchestra_backoffice_block_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $blockId)
    {
        $block = $this->get('open_orchestra_model.repository.block')->findById($blockId);
        if (!$block instanceof BlockInterface) {
            throw new \UnexpectedValueException();
        }

        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $block);

        $formType = $this->get('open_orchestra_backoffice.generate_form_manager')->getFormType($block);
        $form = $this->createForm($formType, $block, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_block_form', array(
                'blockId' => $blockId
            )),
            'method' => 'POST',
            'delete_button' => (0 === $this->get('open_orchestra_model.repository.node')->countBlockUsed($block->getId()))
        ));

        $form->handleRequest($request);
        $message =  $this->get('translator')->trans('open_orchestra_backoffice.form.block.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(BlockEvents::POST_BLOCK_UPDATE, new BlockEvent($block));
        }

        return $this->renderAdminForm($form, array(), null, $form->getConfig()->getAttribute('template'));
    }
}
