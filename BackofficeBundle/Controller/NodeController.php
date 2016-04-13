<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TransverseNodePanelStrategy;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\ModelInterface\Event\NodeEvent;
use OpenOrchestra\ModelInterface\NodeEvents;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NodeController
 */
class NodeController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $id
     *
     * @Config\Route("/node/form/{id}", name="open_orchestra_backoffice_node_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $id)
    {
        $nodeRepository = $this->container->get('open_orchestra_model.repository.node');
        $node = $nodeRepository->findVersionByDocumentId($id);

        $url = $this->generateUrl('open_orchestra_backoffice_node_form', array('id' => $id));
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.node.success');

        $editionRole = $this->getEditionRole($node);
        $form = $this->createForm('oo_node', $node, array('action' => $url), $editionRole);

        $form->handleRequest($request);

        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(NodeEvents::NODE_UPDATE, new NodeEvent($node));
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $parentId
     *
     * @Config\Route("/node/new/{parentId}", name="open_orchestra_backoffice_node_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request, $parentId)
    {
        $parentNode = $this->get('open_orchestra_model.repository.node')->findOneByNodeId($parentId);

        $this->denyAccessUnlessGranted(TreeNodesPanelStrategy::ROLE_ACCESS_CREATE_NODE, $parentNode);

        $contextManager = $this->get('open_orchestra_backoffice.context_manager');
        $language = $contextManager->getCurrentSiteDefaultLanguage();
        $siteId = $contextManager->getCurrentSiteId();
        $node = $this->get('open_orchestra_backoffice.manager.node')->initializeNode($parentId, $language, $siteId);

        $url = $this->generateUrl('open_orchestra_backoffice_node_new', array('parentId' => $parentId));
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.node.success');

        $form = $this->createForm('oo_node', $node, array('action' => $url));

        $form->handleRequest($request);

        if ($this->handleForm($form, $message, $node)) {
            $this->dispatchEvent(NodeEvents::NODE_CREATION, new NodeEvent($node));

            return $this->redirect($this->generateUrl('open_orchestra_backoffice_node_form', array(
                'id' => $node->getId()
            )));
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    protected function getEditionRole(NodeInterface $node)
    {
        if (NodeInterface::TYPE_TRANSVERSE === $node->getNodeType()) {
            return TransverseNodePanelStrategy::ROLE_ACCESS_UPDATE_GENERAL_NODE;
        } elseif (NodeInterface::TYPE_ERROR === $node->getNodeType()) {
            return TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_ERROR_NODE;
        }

        return TreeNodesPanelStrategy::ROLE_ACCESS_UPDATE_NODE;
    }
}
