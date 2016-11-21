<?php

namespace OpenOrchestra\LogBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Symfony\Component\HttpFoundation\Request;
use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;

/**
 * Class LogController
 *
 * @Config\Route("log")
 *
 * @Api\Serialize()
 */
class LogController extends Controller
{
    use HandleRequestDataTable;

    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_log_list")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $mapping = $this
        ->get('open_orchestra.annotation_search_reader')
        ->extractMapping($this->container->getParameter('open_orchestra_log.document.log.class'));

        $repository =  $this->get('open_orchestra_log.repository.log');
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('log_collection');

        return $this->handleRequestDataTable($request, $repository, $mapping, $collectionTransformer);
    }
}
