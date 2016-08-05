<?php

namespace OpenOrchestra\UserAdminBundle\Controller\Api;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\UserBundle\Event\UserEvent;
use OpenOrchestra\UserBundle\UserEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @Config\Route("user")
 *
 * @Api\Serialize()
 */
class UserController extends BaseController
{
    use HandleRequestDataTable;

    /**
     * @param string $userId
     *
     * @Config\Route("/{userId}", name="open_orchestra_api_user_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_USER')")
     *
     * @return FacadeInterface
     */
    public function showAction($userId)
    {
        $user = $this->get('open_orchestra_user.repository.user')->find($userId);

        return $this->get('open_orchestra_api.transformer_manager')->get('user')->transform($user);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_user_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_USER')")
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $mapping = $this
            ->get('open_orchestra.annotation_search_reader')
            ->extractMapping($this->container->getParameter('open_orchestra_user.document.user.class'));

        $repository =  $this->get('open_orchestra_user.repository.user');
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('user_collection');

        return $this->handleRequestDataTable($request, $repository, $mapping, $collectionTransformer);
    }

    /**
     * @param string $groupId
     *
     * @Config\Route("/list-by-group/{groupId}", name="open_orchestra_api_user_list_by_group")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_USER')")
     *
     * @return FacadeInterface
     */
    public function listByGroupAction($groupId)
    {
        $group =  $this->get('open_orchestra_user.repository.group')->find($groupId);
        if (null !== $group) {
            $users = $this->get('open_orchestra_user.repository.user')->findByGroup($group);

            return $this->get('open_orchestra_api.transformer_manager')->get('user_list_group_collection')->transform($users, $group);
        }

        return array();
    }

    /**
     * @param string  $groupId
     * @param Request $request
     *
     * @Config\Route("/list-by-username-without-group/{groupId}", name="open_orchestra_api_user_list_by_username_without_group")
     * @Config\Method({"GET"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_USER')")
     *
     * @return FacadeInterface
     */
    public function listByUsernameWithoutGroupAction(Request $request, $groupId)
    {
        $userName = $request->get('username');
        $group =  $this->get('open_orchestra_user.repository.group')->find($groupId);
        if (null !== $group) {
            $users = $this->get('open_orchestra_user.repository.user')->findByUsernameWithoutGroup($userName, $group);

            return $this->get('open_orchestra_api.transformer_manager')->get('user_list_group_collection')->transform($users, $group);
        }

        return array();
    }

    /**
     * @param string $groupId
     * @param string $userId
     *
     * @Config\Route("/remove-group/{userId}/{groupId}", name="open_orchestra_api_user_remove_group")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_USER')")
     *
     * @return FacadeInterface
     */
    public function removeGroupAction($groupId, $userId)
    {
        $group =  $this->get('open_orchestra_user.repository.group')->find($groupId);
        $user =  $this->get('open_orchestra_user.repository.user')->find($userId);
        $user->removeGroup($group);

        $this->get('object_manager')->flush($user);

        return array();
    }

    /**
     * @param string $groupId
     * @param string $userId
     *
     * @Config\Route("/add-group/{userId}/{groupId}", name="open_orchestra_api_user_add_group")
     * @Config\Method({"POST"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_USER')")
     *
     * @return FacadeInterface
     */
    public function addGroupAction($groupId, $userId)
    {
        $group =  $this->get('open_orchestra_user.repository.group')->find($groupId);
        $user =  $this->get('open_orchestra_user.repository.user')->find($userId);
        $user->addGroup($group);

        $this->get('object_manager')->flush($user);

        return array();
    }

    /**
     * @param int $userId
     *
     * @Config\Route("/{userId}/delete", name="open_orchestra_api_user_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_DELETE_USER')")
     *
     * @return Response
     */
    public function deleteAction($userId)
    {
        $user = $this->get('open_orchestra_user.repository.user')->find($userId);
        $dm = $this->get('object_manager');
        $this->dispatchEvent(UserEvents::USER_DELETE, new UserEvent($user));
        $dm->remove($user);
        $dm->flush();

        return array();
    }
}
