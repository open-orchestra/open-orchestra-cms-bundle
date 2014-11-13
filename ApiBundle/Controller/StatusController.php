<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Transformer\StatusCollectionTransformer;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StatusController
 *
 * @Config\Route("status")
 */
class StatusController extends Controller
{
    /**
     * @Config\Route("", name="php_orchestra_api_status_list")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $statusCollection = $this->get('php_orchestra_model.repository.status')->findAll();

        return $this->get('php_orchestra_api.transformer_manager')->get('status_collection')->transform($statusCollection);
    }

    /**
     * @param int $statusId
     *
     * @Config\Route("/{statusId}/delete", name="php_orchestra_api_status_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($statusId)
    {
        $status = $this->get('php_orchestra_model.repository.status')->find($statusId);
        $this->get('doctrine.odm.mongodb.document_manager')->remove($status);
        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response('', 200);
    }

    /**
     * @param Request $request
     * @param string $nodeId
     *
     * @Config\Route("/allowed-status-change/node/{nodeId}", name="php_orchestra_api_status_allowed_node")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return Response
     */
    public function listAllowedStatusForNodeAction(Request $request, $nodeId)
    {
        $language = $request->get('language');
        $version = $request->get('version');
        $node = $this->get('php_orchestra_model.repository.node')
            ->findOneByNodeIdAndLanguageAndVersionAndSiteId($nodeId, $language, $version);
        $status = $node->getStatus();

        $transitions = $status->getFromRoles();

        $possibleStatutes = array();

        foreach ($transitions as $transition) {
            if ($transition->getToStatus()->isPublished()) {
                $possibleStatutes[] = $transition->getToStatus();
            }
         }

        return $this->get('php_orchestra_api.transformer_manager')->get('status_collection')->transform($possibleStatutes, $status);
    }
}
