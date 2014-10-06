<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use  PHPOrchestra\ModelBundle\Document\Node;

/**
 * Class NodeController
 */
class NodeController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param int     $nodeId
     *
     * @Config\Route("/node/form/{nodeId}", name="php_orchestra_backoffice_node_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $nodeId)
    {
        $nodeRepository = $this->container->get('php_orchestra_model.repository.node');
        $node = $nodeRepository->findOneByNodeIdAndVersion($nodeId);

        $url = $this->generateUrl('php_orchestra_backoffice_node_form', array('nodeId' => $nodeId));
        $message = $this->get('translator')->trans('php_orchestra_backoffice.form.node.success');

        return $this->formHandler($url, $request, $node, $message);
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
        $nodeClass = $this->container->getParameter('php_orchestra_model.document.node.class');
        $node = new $nodeClass();
        $node->setSiteId('1');
        $node->setLanguage('fr');
        $node->setParentId($parentId);

        $url = $this->generateUrl('php_orchestra_backoffice_node_new', array('parentId' => $parentId));
        $message = $this->get('translator')->trans('php_orchestra_backoffice.form.node.success');

        return $this->formHandler($url, $request, $node, $message);
    }

    /**
     * @param String  $url
     * @param Request $request
     * @param Node    $node
     * @param String  $message
     *
     * @return Response
     */
    protected function formHandler($url, Request $request, Node $node, $message)
    {
        $form = $this->createForm(
            'node',
            $node,
            array(
                'action' => $url
            )
        );

        $form->handleRequest($request);

        $this->handleForm($form, $message, $node);

        return $this->renderAdminForm($form);
    }
}
