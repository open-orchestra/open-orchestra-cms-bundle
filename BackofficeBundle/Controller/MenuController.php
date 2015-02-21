<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class MenuController
 */
class MenuController extends Controller
{
    /**
     * @config\Route("/menu", name="open_orchestra_backoffice_menu")
     * @Config\Method({"GET"})
     */
    public function renderAction()
    {
        return $this->render('OpenOrchestraBackofficeBundle:BackOffice/Include:leftPanel.html.twig');
    }
}
