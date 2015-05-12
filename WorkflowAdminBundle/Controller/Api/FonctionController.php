<?php

namespace OpenOrchestra\WorkflowAdminBundle\Controller\Api;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\Fonction\Event\FonctionEvent;
use OpenOrchestra\Fonction\FonctionEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;

/**
 * Class FonctionController
 *
 * @Config\Route("fonction")
 */
class FonctionController extends Controller
{
    /**
     * @param string $fonctionId
     *
     * @Config\Route("/{fonctionId}", name="open_orchestra_api_fonction_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_FONCTION')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($fonctionId)
    {
        $fonction = $this->get('open_orchestra_workflow.repository.fonction')->find($fonctionId);

        return $this->get('open_orchestra_api.transformer_manager')->get('fonction')->transform($fonction);
    }

    /**
     * @Config\Route("", name="open_orchestra_api_fonctions_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_FONCTION')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $fonctionCollection = $this->get('open_orchestra_workflow.repository.fonction')->findAll();

        return $this->get('open_orchestra_api.transformer_manager')->get('fonction_collection')->transform($fonctionCollection);
    }

    /**
     * @param string $fonctionId
     *
     * @Config\Route("/{fonctionId}/delete", name="open_orchestra_api_fonction_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_FONCTION')")
     *
     * @return Response
     */
    public function deleteAction($fonctionId)
    {
        $fonction = $this->get('open_orchestra_workflow.repository.fonction')->find($fonctionId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $this->dispatchEvent(FonctionEvents::FONCTION_DELETE, new FonctionEvent($fonction));
        $dm->remove($fonction);
        $dm->flush();

        return new Response('', 200);
    }
}
