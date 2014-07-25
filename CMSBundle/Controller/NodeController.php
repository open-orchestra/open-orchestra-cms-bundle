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

//        if (empty($nodeId)) {
//            $node = $documentManager->createDocument('Node');
//            $node->setSiteId(1);
//            $node->setLanguage('fr');
//        } else {
            $node = $nodeRepository->findOneByNodeId($nodeId);
            $node->setVersion($node->getVersion() + 1);
//        }

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

        if ($request->isXmlHttpRequest() && $request->get('refreshRecord')) {
            $node->fromArray($request->request->all());
        } elseif ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $node = $form->getData();
        }

        if (
            ($request->get('refreshRecord') || $request->isMethod('POST'))
            && $this->get('validator')->validate($node)
        ) {
           $response['dialog'] = $this->render(
                'PHPOrchestraCMSBundle:BackOffice/Dialogs:confirmation.html.twig',
                array(
                    'dialogId' => '',
                    'dialogTitle' => 'Modification du node',
                    'dialogMessage' => 'Modification ok',
                )
            )->getContent();
            if (!$node->getDeleted()) {
                $node->setId(null);
                $node->setIsNew(true);
                $node->save();

                /*$indexManager = $this->get('php_orchestra_indexation.indexer_manager');
                $indexManager->index($node, 'Node');*/
            } else {
                $this->deleteTree($node->getNodeId());
                $response['redirect'] = $this->generateUrl('php_orchestra_cms_bo_edito');
            }
            return new JsonResponse($response);
        }

        return $this->render(
            'PHPOrchestraCMSBundle:BackOffice/Editorial:template.html.twig',
            array(
                'mainTitle' => 'Gestion des pages',
                'tableTitle' => '',
                'form' => $form->createView()
            )
        );
    }

    /**
     * Recursivly delete a tree
     * 
     * @param string $nodeId
     */
    protected function deleteTree($nodeId)
    {
        /*$indexManager = $this->get('php_orchestra_indexation.indexer_manager');
          $indexManager->deleteIndex($nodeId);*/
        
        $documentManager = $this->get('php_orchestra_cms.document_manager');
        
        $nodeVersions = $documentManager->getDocuments('Node', array('nodeId' => $nodeId));
        
        foreach ($nodeVersions as $node) {
            $node->markAsDeleted();
        };
        
        $sons = $documentManager->getNodeSons($nodeId);
        
        foreach ($sons as $son) {
            $this->deleteTree($son['_id']);
        }
        return true;
    }
}
