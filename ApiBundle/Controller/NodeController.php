<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ModelBundle\Model\NodeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NodeController
 *
 * @Config\Route("node")
 */
class NodeController extends Controller
{
    /**
     * @param Request $request
     * @param string $nodeId
     *
     * @Config\Route("/{nodeId}", name="php_orchestra_api_node_show")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction(Request $request, $nodeId)
    {
        $version = $request->get('version');
        $node = $this->get('php_orchestra_model.repository.node')->findOneByNodeIdAndSiteIdAndLastVersion(
            $nodeId,
            $this->get('php_orchestra_backoffice.context_manager')->getCurrentSiteId()
        );

        return $this->get('php_orchestra_api.transformer_manager')->get('node')->transform($node);
    }

    /**
     * @param string $nodeId
     *
     * @Config\Route("/{nodeId}/delete", name="php_orchestra_api_node_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($nodeId)
    {
        /** @var NodeInterface $node */
        $node = $this->get('php_orchestra_model.repository.node')->findOneByNodeIdAndVersion($nodeId);
        $this->get('php_orchestra_backoffice.manager.node')->deleteTree($node);
        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response('', 200);
    }

    /**
     * @param string $nodeId
     *
     * @Config\Route("/{nodeId}/duplicate", name="php_orchestra_api_node_duplicate")
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function duplicateAction($nodeId)
    {
        /** @var NodeInterface $node */
        $node = $this->get('php_orchestra_model.repository.node')->findOneByNodeIdAndLastVersion($nodeId);
        $newNode = $this->get('php_orchestra_backoffice.manager.node')->duplicateNode($node);
        $em = $this->get('doctrine.odm.mongodb.document_manager');
        $em->persist($newNode);
        $em->flush();

        return new Response('', 200);
    }

    /**
     * @param string $nodeId
     *
     * @Config\Route("/{nodeId}/list-version", name="php_orchestra_api_node_list_version")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return Response
     */
    public function listVersionAction($nodeId)
    {
        $node = $this->get('php_orchestra_model.repository.node')->findByNodeId($nodeId);

        return $this->get('php_orchestra_api.transformer_manager')->get('node_collection')->transformVersions($node);
    }
}
