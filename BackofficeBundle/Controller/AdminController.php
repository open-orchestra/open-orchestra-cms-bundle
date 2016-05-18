<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;

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
    public function adminAction(Request $request, $siteId = null)
    {
        if ($request->getMethod() == 'POST') {
            $this->get('session')->getFlashBag()->add('danger', $this->get('translator')->trans('open_orchestra_backoffice.form.javascript.error'));
        }

        $contextManager = $this->get('open_orchestra_backoffice.context_manager');

        if ($siteId) {
            $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);
            $contextManager->setCurrentsite($site->getSiteId(), $site->getName(), $site->getDefaultLanguage());
        }

        return $this->render('OpenOrchestraBackofficeBundle:BackOffice:home.html.twig');
    }
}
