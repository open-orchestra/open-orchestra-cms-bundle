<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class HomepageController
 */
class HomepageController extends Controller
{
    /**
     * Back Office Home Page
     *
     * @Config\Route("", name="homepage")
     * @Config\Route("/homepage/{siteId}/{language}", name="homepage_with_site_id_and_language")
     *
     * @return Response
     */
    public function homeAction($siteId = null, $language = null)
    {
        $contextManager = $this->get('open_orchestra_backoffice.context_manager');

        if ($language) {
            $contextManager->setCurrentLocale($language);
        }
        if ($siteId) {
            $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);
            $contextManager->setCurrentsite($site->getSiteId(), $site->getName(), $site->getDefaultLanguage());
        }

        return $this->render('OpenOrchestraBackofficeBundle:BackOffice:home.html.twig');
    }
}
