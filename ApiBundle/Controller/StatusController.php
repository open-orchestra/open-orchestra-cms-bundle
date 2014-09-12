<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
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
}
