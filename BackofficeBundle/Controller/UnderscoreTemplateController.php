<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UnderscoreTemplateController
 */
class UnderscoreTemplateController extends Controller
{
    /**
     * @param Request $request
     *
     * @Config\Route("/underscore-template/show", name="open_orchestra_backoffice_underscore_template_show")
     * @Config\Method({"GET"})
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        $response = new Response();
        $response->headers->set('template-id', $request->get('templateId'));

        return $this->render(
            $request->get('templateId') . '._tpl.twig',
            array('language' => $request->get('language')),
            $response
        );
    }
}
