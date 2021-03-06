<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\Backoffice\BusinessRules\Strategies\BusinessActionInterface;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\NodeEvents;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class NodeController
 */
class NodeController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $siteId
     * @param string  $nodeId
     * @param string  $version
     * @param string  $language
     *
     * @Config\Route(
     *     "/node/form/{siteId}/{nodeId}/{language}/{version}",
     *     name="open_orchestra_backoffice_node_form",
     *     defaults={"version": null},
     * )
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $siteId, $nodeId, $language, $version)
    {
        $nodeRepository = $this->get('open_orchestra_model.repository.node');
        if (null === $version) {
            $node = $nodeRepository->findInLastVersion($nodeId, $language, $siteId);
        } else {
            $node = $nodeRepository->findVersionNotDeleted($nodeId, $language, $siteId, $version);
        }
        if (!$node instanceof NodeInterface) {
            throw new \UnexpectedValueException();
        }

        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, $node);

        $url = $this->generateUrl('open_orchestra_backoffice_node_form', array(
            'siteId' => $siteId,
            'nodeId' => $nodeId,
            'version' => $version,
            'language' => $language
        ));

        $template = $node->getTemplate();
        $options = array(
            'action' => $url,
            'enable_delete_button' => $this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(BusinessActionInterface::DELETE, $node),
            'delete_button' => $this->isGranted(ContributionActionInterface::DELETE, $node)
        );
        $form = $this->createForm('oo_node', $node, $options, ContributionActionInterface::EDIT, $node->getStatus());

        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($template !== $node->getTemplate()) {
                $node = $this->get('open_orchestra_backoffice.manager.node')->initializeAreasNode($node);
            }

            $this->get('object_manager')->flush();
            $this->dispatchEvent(NodeEvents::NODE_UPDATE, new NodeEvent($node));

            $message = $this->get('translator')->trans('open_orchestra_backoffice.form.node.success');
            $this->get('session')->getFlashBag()->add('success', $message);
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $siteId
     * @param string  $language
     * @param string  $parentId
     * @param int     $order
     *
     * @Config\Route(
     *     "/node/new/{siteId}/{language}/{parentId}/{order}",
     *     requirements={"order": "\d+"},
     *     name="open_orchestra_backoffice_node_new"
     * )
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request, $siteId, $language, $parentId, $order)
    {
        $order = (int) $order;
        $nodeManager = $this->get('open_orchestra_backoffice.manager.node');
        $nodeRepository = $this->get('open_orchestra_model.repository.node');

        $node = $nodeManager->initializeNode($parentId, $language, $siteId, $order);
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, $node);

        $url = $this->generateUrl('open_orchestra_backoffice_node_new', array(
            'siteId'   => $siteId,
            'language' => $language,
            'parentId' => $parentId,
            'order'    => $order
        ));

        $form = $this->createForm('oo_node', $node, array('action' => $url));

        $form->handleRequest($request);

        if ($nodeRepository->hasNodeWithSameParentAndOrder($parentId, $order, $siteId)) {
            $nodeRepository->updateOrderOfBrothers(
                $siteId,
                $node->getNodeId(),
                $node->getOrder(),
                $node->getParentId()
            );
        }

        if ($form->isValid()) {
            $node = $nodeManager->initializeAreasNode($node);
            $node = $nodeManager->setVersionName($node);

            $nodesEvent = array();
            $documentManager = $this->get('object_manager');
            $documentManager->persist($node);
            $nodesEvent[] = new NodeEvent($node);

            $languages = $this->get('open_orchestra_backoffice.context_backoffice_manager')->getSiteLanguages();
            foreach ($languages as $siteLanguage) {
                if ($language !== $siteLanguage) {
                    $translatedNode = $nodeManager->createNewLanguageNode($node, $siteLanguage);
                    $documentManager->persist($translatedNode);
                    $nodesEvent[] = new NodeEvent($translatedNode);
                }
            }

            $documentManager->flush();

            foreach ($nodesEvent as $nodeEvent) {
                $this->dispatchEvent(NodeEvents::NODE_CREATION, $nodeEvent);
            }

            $message = $this->get('translator')->trans('open_orchestra_backoffice.form.node.success');
            $response = new Response(
                $message,
                Response::HTTP_CREATED,
                array('Content-type' => 'text/plain; charset=utf-8', 'nodeId' => $node->getNodeId())
            );

            return $response;
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param string|\Symfony\Component\Form\FormTypeInterface $type
     * @param null                                             $data
     * @param array                                            $options
     * @param string|null                                      $editionRole
     * @param StatusInterface|null                             $status
     *
     * @return \Symfony\Component\Form\Form
     */
    public function createForm($type, $data = null, array $options = array(), $editionRole = null, StatusInterface $status = null)
    {
        if (null !== $status && $status->isBlockedEdition()) {
            $options['disabled'] = true;
        }

        return parent::createForm($type, $data, $options, $editionRole);
    }
}
