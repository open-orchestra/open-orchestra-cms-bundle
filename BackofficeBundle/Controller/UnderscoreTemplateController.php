<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class UnderscoreTemplateController
 */
class UnderscoreTemplateController extends Controller
{
    /**
     * @param string     $language
     * @param string     $templateId
     *
     * @Config\Route("/underscore-template/show/{language}/{templateId}", name="php_orchestra_backoffice_underscore_template_show")
     * @Config\Method({"GET"})
     *
     * @return Response
     */
    public function showAction($language, $templateId)
    {
        return $this->render(
            'PHPOrchestraBackofficeBundle:BackOffice:Underscore/' . $templateId . '._tpl.twig',
            array('language' => $language)
        );
    }
}
