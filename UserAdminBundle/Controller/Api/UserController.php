<?php

namespace OpenOrchestra\UserAdminBundle\Controller\Api;

use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
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
    /**
     * @param string $userId
     *
     * @Config\Route("/{userId}", name="open_orchestra_api_user_show")
     * @Config\Method({"GET"})
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
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $mapping = array(
            'username' => 'username',
            'groups' => 'groups.label'
        );
        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);
        $repository = $this->get('open_orchestra_user.repository.user');

        if (
            $this->getUser()->hasRole(ContributionRoleInterface::PLATFORM_ADMIN) ||
            $this->getUser()->hasRole(ContributionRoleInterface::DEVELOPER)
        ) {
            $collection = $repository->findForPaginate($configuration);
            $recordsTotal = $repository->count();
            $recordsFiltered = $repository->countWithFilter($configuration);
        } else {
            $sitesId = array();
            $availableSites = $this->get('open_orchestra_backoffice.context_manager')->getAvailableSites();
            foreach ($availableSites as $site) {
                $sitesId[] = $site->getId();
            }

            $collection = $repository->findForPaginateFilterBySitesId($configuration, $sitesId);
            $recordsTotal = $repository->countFilterBySiteId($sitesId);
            $recordsFiltered = $repository->countWithFilterAndSitesId($configuration, $sitesId);
        }


        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('user_collection');
        $facade = $collectionTransformer->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }

    /**
     * @param string $groupId
     *
     * @Config\Route("/list-by-group/{groupId}", name="open_orchestra_api_user_list_by_group")
     * @Config\Method({"GET"})
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
     * @return FacadeInterface
     */
    public function listByUsernameWithoutGroupAction(Request $request, $groupId)
    {
        $userName = $request->get('username');
        $group =  $this->get('open_orchestra_user.repository.group')->find($groupId);
        if (null !== $group) {
            $users = $this->get('open_orchestra_user.repository.user')->findByIncludedUsernameWithoutGroup($userName, $group);

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
     * @param Request $request
     *
     * @Config\Route("/delete/users", name="open_orchestra_api_user_delete_multiple")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteUsersAction(Request $request)
    {
        $format = $request->get('_format', 'json');

        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            $this->getParameter('open_orchestra_user_admin.facade.user_collection.class'),
            $format
        );
        //$this->dispatchEvent(UserEvents::USER_DELETE, new UserEvent($user));

        /*$user = $this->get('open_orchestra_user.repository.user')->find($userId);
        $dm = $this->get('object_manager');
        $this->dispatchEvent(UserEvents::USER_DELETE, new UserEvent($user));
        $dm->remove($user);
        $dm->flush();*/

        return array();
    }
}
