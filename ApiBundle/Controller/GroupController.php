<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\UserBundle\Event\GroupEvent;
use OpenOrchestra\UserBundle\GroupEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class GroupController
 *
 * @Config\Route("group")
 */
class GroupController extends BaseController
{
    /**
     * @param int $groupId
     *
     * @Config\Route("/{groupId}", name="open_orchestra_api_group_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_GROUP')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($groupId)
    {
        $group = $this->get('open_orchestra_user.repository.group')->find($groupId);

        return $this->get('open_orchestra_api.transformer_manager')->get('group')->transform($group);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_group_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_GROUP')")
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
            'name'     => array('key' => 'name'),
        );

        $repository = $this->get('open_orchestra_user.repository.group');

        $groupCollection = $repository->findForPaginateAndSearch($columnsNameToEntityAttribute, $columns, $search, $order, $skip, $limit);
        $recordsTotal = $repository->count();
        $recordsFiltered = $repository->countFilterSearch($columnsNameToEntityAttribute, $columns, $search);

        $facade = $this->get('open_orchestra_api.transformer_manager')->get('group_collection')->transform($groupCollection);
        $facade->setRecordsTotal($recordsTotal);
        $facade->setRecordsFiltered($recordsFiltered);

        return $facade;
    }

    /**
     * @param int $groupId
     *
     * @Config\Route("/{groupId}/delete", name="open_orchestra_api_group_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_GROUP')")
     *
     * @return Response
     */
    public function deleteAction($groupId)
    {
        $group = $this->get('open_orchestra_user.repository.group')->find($groupId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $this->dispatchEvent(GroupEvents::GROUP_DELETE, new GroupEvent($group));
        $dm->remove($group);
        $dm->flush();

        return new Response('', 200);
    }
}
