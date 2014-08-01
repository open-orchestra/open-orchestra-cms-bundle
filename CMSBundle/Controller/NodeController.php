<?php

namespace PHPOrchestra\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\CMSBundle\Exception\NonExistingDocumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NodeController extends Controller
{
    /**
     * Render Node
     *
     * @param int $nodeId
     *
     * @throws NonExistingDocumentException
     * @return Response
     */
    public function showAction($nodeId)
    {
        $node = $this->get('php_orchestra_model.repository.node')->findOneByNodeId($nodeId);
        if (is_null($node)) {
            throw new NonExistingDocumentException("Node not found");
        }

        $response = $this->render(
            'PHPOrchestraCMSBundle:Node:show.html.twig',
            array(
                'node' => $node,
                'datetime' => time()
            )
        );
        
        $response->setPublic();
        $response->setSharedMaxAge(100);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        
        return $response;
    }

    /**
     * @param Request $request
     * @param int $nodeId
     *
     * @return JsonResponse|Response
     */
    public function formAction(Request $request, $nodeId = 0)
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
                'inDialog' => true,
                'beginJs' => array('pagegenerator/dialogNode.js', 'pagegenerator/model.js'),
                'endJs' => array('pagegenerator/node.js?'.time()),
                'action' => $request->getUri()
            )
        );

        return $this->render(
            'PHPOrchestraCMSBundle:BackOffice/Editorial:template.html.twig',
            array(
                'mainTitle' => 'Gestion des pages',
                'tableTitle' => '',
                'form' => $form->createView()
            )
        );
    }
}
