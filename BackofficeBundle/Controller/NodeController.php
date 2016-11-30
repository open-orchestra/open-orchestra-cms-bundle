<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

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
        $node = $this->get('open_orchestra_model.repository.node')->findVersion($nodeId, $language, $siteId, $version);
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

        $status = $node->getStatus();
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.node.success');
        $options = array('action' => $url);
        $form = $this->createForm('oo_node', $node, $options, ContributionActionInterface::EDIT, $node->getStatus());

        $form->handleRequest($request);

        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(NodeEvents::NODE_UPDATE, new NodeEvent($node));
            if ($status->getId() !== $node->getStatus()->getId()) {
                $this->dispatchEvent(NodeEvents::NODE_CHANGE_STATUS, new NodeEvent($node, $status));
                $form = $this->createForm('oo_node', $node, $options, ContributionActionInterface::EDIT, $node->getStatus());
            }
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
        $contextManager = $this->get('open_orchestra_backoffice.context_manager');
        $language = $contextManager->getCurrentSiteDefaultLanguage();
        $siteId = $contextManager->getCurrentSiteId();
        $node = $this->get('open_orchestra_backoffice.manager.node')->initializeNode($parentId, $language, $siteId);

        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, $node);

        $url = $this->generateUrl('open_orchestra_backoffice_node_new', array('parentId' => $parentId));
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.node.success');

        $form = $this->createForm('oo_node', $node, array('action' => $url));

        $form->handleRequest($request);

        if ($this->handleForm($form, $message, $node)) {
            $this->dispatchEvent(NodeEvents::NODE_CREATION, new NodeEvent($node));

            return $this->redirect($this->generateUrl('open_orchestra_backoffice_node_form', array(
                'siteId' => $node->getSiteId(),
                'nodeId' => $node->getNodeId(),
                'language' => $node->getLanguage(),
                'version' => $node->getVersion(),
            )));
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
