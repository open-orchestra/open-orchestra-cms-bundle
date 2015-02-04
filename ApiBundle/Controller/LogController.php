<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;

/**
 * Class LogController
 *
 * @Config\Route("log")
 */
class LogController extends Controller
{
    /**
     * @Config\Route("", name="php_orchestra_api_log_list")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $logCollection = $this->get('php_orchestra_log.repository.log')->findAll();

        return $this->get('php_orchestra_api.transformer_manager')->get('log_collection')->transform($logCollection);
    }
}
