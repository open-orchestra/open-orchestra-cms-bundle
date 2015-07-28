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
     * @Config\Route("/{siteId}/homepage/{_locale}", name="homepage_with_site_id_and_language")
     *
     * @return Response
     */
    public function adminAction($siteId = null)
    {
        $contextManager = $this->get('open_orchestra_backoffice.context_manager');

        if ($siteId) {
            $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);
            $contextManager->setCurrentsite($site->getSiteId(), $site->getName(), $site->getDefaultLanguage());
        }

        return $this->render('OpenOrchestraBackofficeBundle:BackOffice:home.html.twig');
    }
}
