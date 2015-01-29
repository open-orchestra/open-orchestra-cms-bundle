<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\BackofficeBundle\Event\NodeEvent;
use PHPOrchestra\BackofficeBundle\NodeEvents;
use PHPOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\Form\Form;
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
     * @Config\Route("/node/form/{id}", name="php_orchestra_backoffice_node_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $id)
    {
        $nodeRepository = $this->container->get('php_orchestra_model.repository.node');
        $node = $nodeRepository->find($id);

        $url = $this->generateUrl('php_orchestra_backoffice_node_form', array('id' => $id));
        $message = $this->get('translator')->trans('php_orchestra_backoffice.form.node.success');

        $form = $this->generateForm($node, $url);

        $form->handleRequest($request);

        $this->handleForm($form, $message, $node);

        $this->dispatchEvent(NodeEvents::NODE_UPDATE, new NodeEvent($node));

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $parentId
     *
     * @Config\Route("/node/new/{parentId}", name="php_orchestra_backoffice_node_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request, $parentId)
    {
        $node = $this->get('php_orchestra_backoffice.manager.node')->initializeNewNode();
        $node->setParentId($parentId);

        $url = $this->generateUrl('php_orchestra_backoffice_node_new', array('parentId' => $parentId));
        $message = $this->get('translator')->trans('php_orchestra_backoffice.form.node.success');

        $form = $this->generateForm($node, $url);

        $form->handleRequest($request);

        $this->handleForm($form, $message, $node);

        $statusCode = 200;
        if ($form->getErrors()->count() > 0) {
            $statusCode = 400;
        } elseif (!is_null($node->getNodeId())) {
            $url = $this->generateUrl('php_orchestra_backoffice_node_form', array('id' => $node->getId()));

            $this->dispatchEvent(NodeEvents::NODE_CREATION, new NodeEvent($node));

            return $this->redirect($url);
        };

        $response = new Response('', $statusCode, array('Content-type' => 'text/html; charset=utf-8'));

        return $this->render(
            'PHPOrchestraBackofficeBundle:Editorial:template.html.twig',
            array('form' => $form->createView()),
            $response
        );
    }

    /**
     * @param NodeInterface $node
     * @param string        $url
     *
     * @return Form
     */
    protected function generateForm($node, $url)
    {
        $form = $this->createForm(
            'node',
            $node,
            array(
                'action' => $url
            )
        );

        return $form;
    }
}
