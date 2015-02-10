<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ParameterController
 *
 * @Config\Route("parameter")
 */
class ParameterController extends BaseController
{
    /**
     * @Config\Route("/languages", name="php_orchestra_api_parameter_languages_show")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return Response
     */
    public function getAllLanguagesAction()
    {
        return array('languages' => array_keys($this->container->getParameter('php_orchestra_backoffice.orchestra_choice.front_language')));
    }
}
