<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class MenuController
 */

class MenuController extends Controller {

    /**
     * @config\Route("/menu", name="php_orchestra_backoffice_menu")
     * @Config\Method({"GET"})
     */
    public function renderAction()
    {
        return $this->render('PHPOrchestraBackofficeBundle:BackOffice:Include/leftMenu.html.twig');
    }
}
