<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ModelInterface\Event\RedirectionEvent;
use PHPOrchestra\ModelInterface\RedirectionEvents;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

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
     * @Config\Route("/{redirectionId}", name="php_orchestra_api_redirection_show")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($redirectionId)
    {
        $redirection = $this->get('php_orchestra_model.repository.redirection')->find($redirectionId);

        return $this->get('php_orchestra_api.transformer_manager')->get('redirection')->transform($redirection);
    }

    /**
     * @Config\Route("", name="php_orchestra_api_redirection_list")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $redirectionCollection = $this->get('php_orchestra_model.repository.redirection')->findAll();

        return $this->get('php_orchestra_api.transformer_manager')->get('redirection_collection')->transform($redirectionCollection);
    }

    /**
     * @param int $redirectionId
     *
     * @Config\Route("/{redirectionId}/delete", name="php_orchestra_api_redirection_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($redirectionId)
    {
        $redirection = $this->get('php_orchestra_model.repository.redirection')->find($redirectionId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $this->dispatchEvent(RedirectionEvents::REDIRECTION_DELETE, new RedirectionEvent($redirection));
        $dm->remove($redirection);
        $dm->flush();

        return new Response('', 200);
    }
}
