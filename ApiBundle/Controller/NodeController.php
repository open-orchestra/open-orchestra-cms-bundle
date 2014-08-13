<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;

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
     * @param Request    $request
     * @param string|int $nodeId
     *
     * @Config\Route("/{nodeId}", name="php_orchestra_api_node_edit", defaults={"nodeId" = 0 })
     * @Config\Method({"POST"})
     *
     * @return JsonResponse
     */
    public function editAction(Request $request, $nodeId = 0)
    {
        $nodeRepository = $this->container->get('php_orchestra_model.repository.node');

        $node = $nodeRepository->findOneByNodeId($nodeId);
        if (!empty($node)) {
            $node->setVersion($node->getVersion() + 1);
            $node->removeAllArea();
        }

        $facade = $this->get('jms_serializer')->deserialize($request->getContent(), 'PHPOrchestra\ApiBundle\Facade\NodeFacade', $request->get('_format', 'json'));

        $node = $this->get('php_orchestra_api.transformer_manager')->get('node')->reverseTransform($facade, $node);

        $violations = $this->get('validator')->validate($node);
        if (0 === count($violations)) {
            $response['dialog'] = $this->render(
                'PHPOrchestraCMSBundle:BackOffice/Dialogs:confirmation.html.twig',
                array(
                    'dialogId' => '',
                    'dialogTitle' => 'Modification du node',
                    'dialogMessage' => 'Modification ok',
                )
            )->getContent();
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            if (!$node->getDeleted()) {
                $documentManager->persist($node);

                /*$indexManager = $this->get('php_orchestra_indexation.indexer_manager');
                $indexManager->index($node, 'Node');*/
            } else {
                $this->deleteTree($node->getNodeId());
                $response['redirect'] = $this->generateUrl('php_orchestra_cms_bo_edito');
            }
            $documentManager->flush();

            return new JsonResponse($response);
        }

        return new JsonResponse($this->get('serializer')->serialize($violations, $request->get('_format', 'json')), 400);
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

        $nodeRepository = $this->container->get('php_orchestra_model.repository.node');

        $nodeVersions = $nodeRepository->findByNodeId($nodeId);

        foreach ($nodeVersions as $node) {
            $node->setDeleted(true);
        };

        $sons = $nodeRepository->findByParentId($nodeId);

        foreach ($sons as $son) {
            $this->deleteTree($son->getNodeId());
        }
    }
}
