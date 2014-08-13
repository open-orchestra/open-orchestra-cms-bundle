<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Config\Route("/admin/node/form/{nodeId}", name="php_orchestra_backoffice_node_form", defaults={"nodeId" = 0})
     * @Config\Method({"GET", "POST"})
     *
     * @return JsonResponse|Response
     */
    public function formAction(Request $request, $nodeId)
    {
        $nodeRepository = $this->container->get('php_orchestra_model.repository.node');

        if (empty($nodeId)) {
            $nodeClass = $this->container->getParameter('php_orchestra_model.document.node.class');
            $node = new $nodeClass();
            $node->setSiteId(1);
            $node->setLanguage('fr');
        } else {
            $node = $nodeRepository->findOneByNodeId($nodeId);
            $node->setVersion($node->getVersion() + 1);
        }

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

            return $this->redirect($this->generateUrl('php_orchestra_cms_bo'));
        }

        return $this->render(
            'PHPOrchestraBackofficeBundle:Editorial:template.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }
}
