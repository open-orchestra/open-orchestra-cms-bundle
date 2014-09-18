<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ModelBundle\Model\NodeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NodeController
 *
 * @Config\Route("node")
 */
class NodeController extends Controller
{
    /**
     * @param string $nodeId
     *
     * @Config\Route("/{nodeId}", name="php_orchestra_api_node_show")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($nodeId)
    {
        $node = $this->get('php_orchestra_model.repository.node')->findOneByNodeId($nodeId);

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
        $node = $this->get('php_orchestra_model.repository.node')->findOneByNodeId($nodeId);
        $this->deleteTree($node);
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
        $node = $this->get('php_orchestra_model.repository.node')->findOneByNodeId($nodeId);
        $newNode = clone $node;
        $newNode->setVersion($node->getVersion() + 1);
        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response('', 200);
    }

    /**
     * @param NodeInterface $node
     */
    protected function deleteTree(NodeInterface $node)
    {
        $node->setDeleted(true);

        $nodeRepository = $this->get('php_orchestra_model.repository.node');
        $sons = $nodeRepository->findByParentId($node->getNodeId());

        foreach ($sons as $son) {
            $this->deleteTree($son);
        }
    }
    
    
    
}
