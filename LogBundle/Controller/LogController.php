<?php

namespace OpenOrchestra\LogBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
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
        $columns = $request->get('columns');
        $search = $request->get('search');
        $search = (null !== $search && isset($search['value'])) ? $search['value'] : null;
        $order = $request->get('order');
        $skip = $request->get('start');
        $skip = (null !== $skip) ? (int)$skip : null;
        $limit = $request->get('length');
        $limit = (null !== $limit) ? (int)$limit : null;

        $columnsNameToEntityAttribute = array(
            'date_time' => array('key' => 'datetime'),
            'user_ip'   => array('key' => 'extra.user_ip'),
            'user_name' => array('key' => 'extra.user_name'),
            'site_name' => array('key' => 'extra.site_name'),
            'message'   => array('key' => 'extra.message'),
        );

        $repository =  $this->get('open_orchestra_log.repository.log');

        $logCollection = $repository->findForPaginateAndSearch($columnsNameToEntityAttribute, $columns, $search, $order, $skip, $limit);
        $recordsTotal = $repository->count();
        $recordsFiltered = $repository->countFilterSearch($columnsNameToEntityAttribute, $columns, $search);

        $facade = $this->get('open_orchestra_api.transformer_manager')->get('log_collection')->transform($logCollection);
        $facade->setRecordsTotal($recordsTotal);
        $facade->setRecordsFiltered($recordsFiltered);

        return $facade;
    }
}
