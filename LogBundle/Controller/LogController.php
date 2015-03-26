<?php

namespace OpenOrchestra\LogBundle\Controller;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\ApiBundle\Controller\Annotation as Api;

/**
 * Class LogController
 *
 * @Config\Route("log")
 */
class LogController extends Controller
{
    /**
     * @Config\Route("", name="open_orchestra_api_log_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_LOG')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $logCollection = $this->get('open_orchestra_log.repository.log')->findAll();

        return $this->get('open_orchestra_api.transformer_manager')->get('log_collection')->transform($logCollection);
    }
}
