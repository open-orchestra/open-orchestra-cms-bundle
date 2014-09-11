<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class NodeController
 */
class NodeController extends Controller
{
    /**
     * @param Request $request
     * @param int     $nodeId
     *
     * @Config\Route("/node/form/{nodeId}", name="php_orchestra_backoffice_node_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return RedirectResponse|Response
     */
    public function formAction(Request $request, $nodeId)
    {
        $nodeRepository = $this->container->get('php_orchestra_model.repository.node');

        $node = $nodeRepository->findOneByNodeId($nodeId);

        $form = $this->createForm(
            'node',
            $node,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_node_form', array('nodeId' => $nodeId))
            )
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->get('doctrine.odm.mongodb.document_manager');
            $em->persist($node);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('homepage')
                . '#' . $nodeId
            );
        }

        return $this->render(
            'PHPOrchestraBackofficeBundle:Editorial:template.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * @param Request $request
     * @param string  $parentId
     *
     * @Config\Route("/node/new/{parentId}", name="php_orchestra_backoffice_node_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request, $parentId)
    {
        $nodeClass = $this->container->getParameter('php_orchestra_model.document.node.class');
        $node = new $nodeClass();
        $node->setSiteId(1);
        $node->setLanguage('fr');
        $node->setParentId($parentId);

        $form = $this->createForm(
            'node',
            $node,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_node_new', array('parentId' => $parentId))
            )
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->get('doctrine.odm.mongodb.document_manager');
            $em->persist($node);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('homepage')
                . '#' . $node->getNodeId()
            );
        }

        return $this->render(
            'PHPOrchestraBackofficeBundle:Editorial:template.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }
}
