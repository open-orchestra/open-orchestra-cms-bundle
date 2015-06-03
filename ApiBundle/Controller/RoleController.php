<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\RoleEvent;
use OpenOrchestra\ModelInterface\RoleEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class RoleController
 *
 * @Config\Route("role")
 */
class RoleController extends BaseController
{
    /**
     * @param int $roleId
     *
     * @Config\Route("/{roleId}", name="open_orchestra_api_role_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_ROLE')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($roleId)
    {
        $role = $this->get('open_orchestra_model.repository.role')->find($roleId);

        return $this->get('open_orchestra_api.transformer_manager')->get('role')->transform($role);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_role_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_ROLE')")
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
            'description' => array('key' => 'name'),
            'from_status' => array('key' => 'fromStatus.name'),
            'to_status'   => array('key' => 'toStatus.name'),
        );

        $repository = $this->get('open_orchestra_model.repository.role');
        $roleCollection = $repository->findForPaginateAndSearch($columnsNameToEntityAttribute, $columns, $search, $order, $skip, $limit);
        $recordsTotal = $repository->count();
        $recordsFiltered = $repository->countFilterSearch($columnsNameToEntityAttribute, $columns, $search);

        $facade = $this->get('open_orchestra_api.transformer_manager')->get('role_collection')->transform($roleCollection);
        $facade->setRecordsTotal($recordsTotal);
        $facade->setRecordsFiltered($recordsFiltered);

        return $facade;
    }

    /**
     * @param int $roleId
     *
     * @Config\Route("/{roleId}/delete", name="open_orchestra_api_role_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_ROLE')")
     *
     * @return Response
     */
    public function deleteAction($roleId)
    {
        $role = $this->get('open_orchestra_model.repository.role')->find($roleId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $this->dispatchEvent(RoleEvents::ROLE_DELETE, new RoleEvent($role));
        $dm->remove($role);
        $dm->flush();

        return new Response('', 200);
    }
}
