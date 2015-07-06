<?php

namespace OpenOrchestra\LogBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LogController
 *
 * @Config\Route("log")
 */
class LogController extends Controller
{
    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_log_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_LOG')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $repository =  $this->get('open_orchestra_log.repository.log');
        $configuration = PaginateFinderConfiguration::generateFromRequest($request);
        $configuration->setDescriptionEntity(array(
                'date_time' => array('key' => 'datetime'),
                'user_ip'   => array('key' => 'extra.user_ip'),
                'user_name' => array('key' => 'extra.user_name'),
                'site_name' => array('key' => 'extra.site_name'),
                'message'   => array('key' => 'extra.message'),
         ));

        $logCollection = $repository->findForPaginate($configuration);
        $recordsTotal = $repository->count();
        $recordsFiltered = $repository->countWithFilter($configuration);

        $facade = $this->get('open_orchestra_api.transformer_manager')->get('log_collection')->transform($logCollection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }
}
