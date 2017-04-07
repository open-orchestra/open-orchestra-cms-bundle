<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\NodeNotFoundHttpException;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\BlockNodeEvents;
use OpenOrchestra\ModelInterface\Event\BlockNodeEvent;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
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
     * @Config\Method({"GET", "POST", "PATCH"})
     *
     * @return Response
     */
    public function newSharedBlockAction(Request $request, $component, $language)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, BlockInterface::ENTITY_TYPE);

        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $block = $this->get('open_orchestra_backoffice.manager.block')->initializeBlock($component, $siteId, $language, true);
        $form = $this->createBlockForm($request, array(
            "action" => $this->generateUrl('open_orchestra_backoffice_shared_block_new', array(
                'component' => $component,
                'language' => $language,
            )),
            "new_button" => true
        ), $block);

        $form->handleRequest($request);

        if ('PATCH' !== $request->getMethod() && $form->isValid()) {
            $documentManager = $this->get('object_manager');
            $documentManager->persist($block);
            $documentManager->flush();
            $message = $this->get('translator')->trans('open_orchestra_backoffice.form.block.creation');

            $this->dispatchEvent(BlockEvents::POST_BLOCK_CREATE, new BlockEvent($block));
            $response = new Response(
                $message,
                Response::HTTP_CREATED,
                array('Content-type' => 'text/plain; charset=utf-8', 'blockId' => $block->getId())
            );

            return $response;
        }

        return $this->renderAdminForm($form, array(), null, $form->getConfig()->getAttribute('template'));
    }

    /**
     * @param Request $request
     * @param string  $nodeId
     * @param string  $language
     * @param string  $version
     * @param string  $areaId
     * @param string  $position
     * @param string  $component
     *
     * @Config\Route("/block/new-in-node/{nodeId}/{language}/{version}/{areaId}/{position}/{component}", name="open_orchestra_backoffice_block_new_in_node")
     * @Config\Method({"GET", "POST", "PATCH"})
     *
     * @return Response
     * @throws NodeNotFoundHttpException
     * @throws \OpenOrchestra\Backoffice\Exception\MissingGenerateFormStrategyException
     */
    public function newAction(Request $request, $nodeId, $language, $version, $areaId, $position, $component)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, BlockInterface::ENTITY_TYPE);

        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $block = $this->get('open_orchestra_backoffice.manager.block')->initializeBlock($component, $siteId, $language, false);
        $form = $this->createBlockForm($request, array(
            "action" => $this->generateUrl('open_orchestra_backoffice_block_new_in_node', array(
                'nodeId'    => $nodeId,
                'language'  => $language,
                'version'   => $version,
                'areaId'    => $areaId,
                'position'  => $position,
                'component' => $component,
            ))
        ), $block);

        $form->handleRequest($request);

        if ('PATCH' !== $request->getMethod() && $form->isValid()) {
            $node = $this->get('open_orchestra_model.repository.node')->findVersionNotDeleted($nodeId, $language, $siteId, $version);
            if (!$node instanceof NodeInterface) {
                throw new NodeNotFoundHttpException();
            }
            $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $node);
            $this->addBlockToNode($block, $node, $areaId, $position);

            return new Response('', Response::HTTP_CREATED, array('Content-type' => 'text/html; charset=utf-8'));
        }

        return $this->renderAdminForm($form, array(), null, $form->getConfig()->getAttribute('template'));
    }

    /**
     * @param Request $request
     * @param string  $blockId
     *
     * @Config\Route("/block/form/{blockId}", name="open_orchestra_backoffice_block_form")
     * @Config\Method({"GET", "POST", "PATCH"})
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

        $form = $this->createBlockForm($request, array(
            "action" => $this->generateUrl('open_orchestra_backoffice_block_form', array(
                'blockId' => $blockId
            )),
            "delete_button" => $this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(ContributionActionInterface::DELETE, $block)
        ), $block);

        $form->handleRequest($request);
        $message =  $this->get('translator')->trans('open_orchestra_backoffice.form.block.success');
        if ('PATCH' !== $request->getMethod() && $this->handleForm($form, $message)) {
            $this->dispatchEvent(BlockEvents::POST_BLOCK_UPDATE, new BlockEvent($block));
        }

        return $this->renderAdminForm($form, array(), null, $form->getConfig()->getAttribute('template'));
    }

    /**
     * @param BlockInterface $block
     * @param NodeInterface  $node
     * @param string         $areaId
     * @param string         $position
     */
    protected function addBlockToNode(BlockInterface $block, NodeInterface $node, $areaId, $position)
    {
        $documentManager = $this->get('object_manager');

        $area = $node->getArea($areaId);
        $area->addBlock($block, $position);

        $documentManager->persist($block);
        $documentManager->flush();

        $this->dispatchEvent(BlockEvents::POST_BLOCK_CREATE, new BlockEvent($block));
        $this->dispatchEvent(BlockNodeEvents::ADD_BLOCK_TO_NODE, new BlockNodeEvent($node, $block));
    }

    /**
     * @param Request        $request
     * @param string         $option
     * @param BlockInterface $block
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createBlockForm(Request $request, $option, BlockInterface $block)
    {
        $formType = $this->get('open_orchestra_backoffice.generate_form_manager')->getFormType($block);

        $method = "POST";
        if ("PATCH" === $request->getMethod()) {
            $option["validation_groups"] = false;
            $method = "PATCH";
        }
        $option["method"] = $method;

        return $this->createForm($formType, $block, $option);
    }
}
