<?php

namespace OpenOrchestra\LogBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Symfony\Component\HttpFoundation\Request;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\LogBundle\Model\LogInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class LogController
 *
 * @Config\Route("log")
 *
 * @Api\Serialize()
 */
class LogController extends BaseController
{
    /**
     * @param Request $request
     *
     * @return FacadeInterface
     *
     * @Config\Route("", name="open_orchestra_api_log_list")
     * @Config\Method({"GET"})
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, LogInterface::ENTITY_TYPE);
        $mapping = array(
            'date_time' => 'datetime',
            'user_ip'   => 'extra.user_ip',
            'user_name' => 'extra.user_name',
            'message'   => 'message'
        );
        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $configuration->addSearch('site_id', $siteId);

        $repository =  $this->get('open_orchestra_log.repository.log');
        $collection = $repository->findForPaginate($configuration);
        $recordsTotal = $repository->count();
        $recordsFiltered = $repository->countWithFilter($configuration);
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('log_collection');
        $facade = $collectionTransformer->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }
}
