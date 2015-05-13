<?php

namespace OpenOrchestra\WorkflowFonctionAdminBundle\Controller\Api;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\WorkflowFonction\Event\WorkflowFonctionEvent;
use OpenOrchestra\WorkflowFonction\WorkflowFonctionEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;

/**
 * Class WorkflowFonctionController
 *
 * @Config\Route("workflowfonction")
 */
class WorkflowFonctionController extends Controller
{
    /**
     * @param string $workflowFonctionId
     *
     * @Config\Route("/{workflowFonctionId}", name="open_orchestra_api_workflowfonction_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_WORKFLOWFONCTION')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($workflowFonctionId)
    {
        $workflowFonction = $this->get('open_orchestra_workflowfonction.repository.workflowfonction')->find($workflowFonctionId);

        return $this->get('open_orchestra_api.transformer_manager')->get('workflowfonction')->transform($workflowFonction);
    }

    /**
     * @Config\Route("", name="open_orchestra_api_workflowfonctions_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_WORKFLOWFONCTION')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $workflowFonctionCollection = $this->get('open_orchestra_workflowfonction.repository.workflowfonction')->findAll();

        return $this->get('open_orchestra_api.transformer_manager')->get('workflowfonction_collection')->transform($workflowFonctionCollection);
    }

    /**
     * @param string $workflowFonctionId
     *
     * @Config\Route("/{workflowFonctionId}/delete", name="open_orchestra_api_workflowfonction_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_WORKFLOWFONCTION')")
     *
     * @return Response
     */
    public function deleteAction($workflowFonctionId)
    {
        $workflowFonction = $this->get('open_orchestra_workflowfonction.repository.workflowfonction')->find($workflowFonctionId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $this->dispatchEvent(WorkflowFonctionEvents::WORKFLOWFONCTION_DELETE, new WorkflowFonctionEvent($workflowFonction));
        $dm->remove($workflowFonction);
        $dm->flush();

        return new Response('', 200);
    }
}
