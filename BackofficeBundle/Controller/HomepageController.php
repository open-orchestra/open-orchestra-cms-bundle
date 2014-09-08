<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

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
     *
     * @return Response
     */
    public function homeAction()
    {
        return $this->render('PHPOrchestraBackofficeBundle:BackOffice:home.html.twig');
    }
}
