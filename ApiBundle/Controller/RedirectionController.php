<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\RedirectionEvent;
use OpenOrchestra\ModelInterface\RedirectionEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class RedirectionController
 *
 * @Config\Route("redirection")
 */
class RedirectionController extends BaseController
{
    /**
     * @param int $redirectionId
     *
     * @Config\Route("/{redirectionId}", name="open_orchestra_api_redirection_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REDIRECTION')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($redirectionId)
    {
        $redirection = $this->get('open_orchestra_model.repository.redirection')->find($redirectionId);

        return $this->get('open_orchestra_api.transformer_manager')->get('redirection')->transform($redirection);
    }

    /**
     * @Config\Route("", name="open_orchestra_api_redirection_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REDIRECTION')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $redirectionCollection = $this->get('open_orchestra_model.repository.redirection')->findAll();

        return $this->get('open_orchestra_api.transformer_manager')->get('redirection_collection')->transform($redirectionCollection);
    }

    /**
     * @param int $redirectionId
     *
     * @Config\Route("/{redirectionId}/delete", name="open_orchestra_api_redirection_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_REDIRECTION')")
     *
     * @return Response
     */
    public function deleteAction($redirectionId)
    {
        $redirection = $this->get('open_orchestra_model.repository.redirection')->find($redirectionId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $this->dispatchEvent(RedirectionEvents::REDIRECTION_DELETE, new RedirectionEvent($redirection));
        $dm->remove($redirection);
        $dm->flush();

        return new Response('', 200);
    }
}
