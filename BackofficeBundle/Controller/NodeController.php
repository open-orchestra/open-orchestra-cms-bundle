<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\ModelBundle\Model\NodeInterface;
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
        $contextManager = $this->get('php_orchestra_backoffice.context_manager');
        $siteRepository = $this->container->get('php_orchestra_model.repository.site');
        $site = $siteRepository->findOneBySiteId($contextManager->getCurrentSiteId());
        if ($site) {
            $theme = $site->getTheme();
        }

        $nodeClass = $this->container->getParameter('php_orchestra_model.document.node.class');
        $node = new $nodeClass();
        $node->setSiteId($contextManager->getCurrentSiteId());
        $node->setLanguage($contextManager->getCurrentLocale());
        $node->setParentId($parentId);
        if ($theme) {
            $node->setTheme($theme->getName());
        }

        $url = $this->generateUrl('php_orchestra_backoffice_node_new', array('parentId' => $parentId));
        $message = $this->get('translator')->trans('php_orchestra_backoffice.form.node.success');

        $form = $this->generateForm($node, $url);

        $form->handleRequest($request);

        $this->handleForm($form, $message, $node);

        if ($form->getErrors()->count() > 0) {
            $statusCode = 400;
        } elseif (!is_null($node->getNodeId())) {
                $url = $this->generateUrl('php_orchestra_backoffice_node_form', array('id' => $node->getId()));

                return $this->redirect($url);
        } else {
            $statusCode = 200;
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
