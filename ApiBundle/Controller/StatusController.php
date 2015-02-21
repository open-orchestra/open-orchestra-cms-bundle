<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\StatusableEvent;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\StatusEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OpenOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StatusController
 *
 * @Config\Route("status")
 */
class StatusController extends Controller
{
    /**
     * @Config\Route("", name="open_orchestra_api_status_list")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $statusCollection = $this->get('open_orchestra_model.repository.status')->findAll();

        return $this->get('open_orchestra_api.transformer_manager')->get('status_collection')->transform($statusCollection);
    }

    /**
     * @param int $statusId
     *
     * @Config\Route("/{statusId}/delete", name="open_orchestra_api_status_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($statusId)
    {
        $status = $this->get('open_orchestra_model.repository.status')->find($statusId);
        $this->dispatchEvent(StatusEvents::STATUS_DELETE, new StatusableEvent($status));
        $this->get('doctrine.odm.mongodb.document_manager')->remove($status);
        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response('', 200);
    }

    /**
     * @param string $nodeMongoId
     *
     * @Config\Route("/list-statuses/node/{nodeMongoId}", name="open_orchestra_api_list_status_node")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return Response
     */
    public function listStatusesForNodeAction($nodeMongoId)
    {
        $node = $this->get('open_orchestra_model.repository.node')->find($nodeMongoId);

        return $this->listStatuses($node->getStatus());
    }

    /**
     * @param string $contentMongoId
     *
     * @Config\Route("/list-statuses/content/{contentMongoId}", name="open_orchestra_api_list_status_content")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return Response
     */
    public function listStatusesForContentAction($contentMongoId)
    {
        $content = $this->get('open_orchestra_model.repository.content')->find($contentMongoId);

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

        return $this->get('open_orchestra_api.transformer_manager')->get('status_collection')->transform($possibleStatuses, $status);
    }
}
