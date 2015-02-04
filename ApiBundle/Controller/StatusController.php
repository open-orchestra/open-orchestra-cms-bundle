<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Transformer\StatusCollectionTransformer;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ModelInterface\Event\StatusableEvent;
use PHPOrchestra\ModelInterface\Model\StatusInterface;
use PHPOrchestra\ModelInterface\StatusEvents;
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
        $this->dispatchEvent(StatusEvents::STATUS_DELETE, new StatusableEvent($status));
        $this->get('doctrine.odm.mongodb.document_manager')->remove($status);
        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response('', 200);
    }

    /**
     * @param string $nodeMongoId
     *
     * @Config\Route("/list-statuses/node/{nodeMongoId}", name="php_orchestra_api_list_status_node")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return Response
     */
    public function listStatusesForNodeAction($nodeMongoId)
    {
        $node = $this->get('php_orchestra_model.repository.node')->find($nodeMongoId);

        return $this->listStatuses($node->getStatus());
    }

    /**
     * @param string $contentMongoId
     *
     * @Config\Route("/list-statuses/content/{contentMongoId}", name="php_orchestra_api_list_status_content")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return Response
     */
    public function listStatusesForContentAction($contentMongoId)
    {
        $content = $this->get('php_orchestra_model.repository.content')->find($contentMongoId);

        return $this->listStatuses($content->getStatus());
    }

    /**
     * @param StatusInterface $status
     *
     * @return Response
     */
    protected function listStatuses(StatusInterface $status)
    {
        $transitions = $status->getFromRoles();

        $possibleStatuses = array();

        foreach ($transitions as $transition) {
            $possibleStatuses[] = $transition->getToStatus();
        }

        return $this->get('php_orchestra_api.transformer_manager')->get('status_collection')->transform($possibleStatuses, $status);
    }
}
