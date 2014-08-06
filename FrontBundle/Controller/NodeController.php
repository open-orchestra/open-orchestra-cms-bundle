<?php

namespace PHPOrchestra\FrontBundle\Controller;

use PHPOrchestra\CMSBundle\Exception\NonExistingDocumentException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class NodeController
 */
class NodeController extends Controller
{
    /**
     * Render Node
     *
     * @param int $nodeId
     *
     * @Config\Route("/node/{nodeId}", name="php_orchestra_front_node")
     * @Config\Method({"GET"})
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

}
 