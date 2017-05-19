<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class AdminController
 */
class AdminController extends Controller
{
    /**
     * Back Office
     *
     * @param string|null $siteId
     *
     * @Config\Route("", name="homepage")
     * @Config\Route("/{siteId}/homepage", name="homepage_with_site_id_and_language")
     *
     * @return Response
     */
    public function adminAction($siteId = null)
    {
        $contextManager = $this->get('open_orchestra_backoffice.context_backoffice_manager');

        if ($siteId) {
            $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);
            $contextManager->setSite($site->getSiteId(), $site->getName(), $site->getDefaultLanguage(), $site->getLanguages());
        }
        $clientConfiguration = $this->get('open_orchestra_backoffice.manager.client_configuration');

        return $this->render('OpenOrchestraBackofficeBundle::layout.html.twig' , array(
            'clientConfiguration' => $clientConfiguration->getClientConfiguration()
        ));
    }

    /**
     * @Config\Route("/clear-context", name="clearContext")
     */
    public function cleanContextAction()
    {
        dump($this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'));
        $this->get('open_orchestra_backoffice.context_backoffice_manager')->clearContext();

        return new Response();
    }
}
