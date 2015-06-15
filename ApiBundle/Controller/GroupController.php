<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
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
    use HandleRequestDataTable;

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
        $columnsNameToEntityAttribute = array(
            'name'     => array('key' => 'name'),
        );
        $repository = $this->get('open_orchestra_user.repository.group');
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('group_collection');

        return $this->handleRequestDataTable($request, $repository, $columnsNameToEntityAttribute, $collectionTransformer);
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
