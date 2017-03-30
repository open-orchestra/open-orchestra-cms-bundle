<?php

namespace OpenOrchestra\UserAdminBundle\Controller\Api;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\BaseApi\Exceptions\HttpException\UserNotFoundHttpException;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\UserBundle\Event\UserEvent;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\UserBundle\UserEvents;
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
     * @param string $email
     *
     * @Config\Route("/{email}", name="open_orchestra_api_user_show")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function showAction($email)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, UserInterface::ENTITY_TYPE);
        $user = $this->get('open_orchestra_user.repository.user')->findOneByEmail($email);
        if ($user instanceof UserInterface) {
            return $this->get('open_orchestra_api.transformer_manager')->get('user')->transform($user);
        }

        return array();
    }

    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_user_list")
     * @Config\Method({"GET"})
     * @Api\Groups({\OpenOrchestra\ApiBundle\Context\CMSGroupContext::AUTHORIZATIONS})
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, UserInterface::ENTITY_TYPE);
        $mapping = array(
            'username' => 'username',
            'groups' => 'groups.label'
        );
        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);
        $repository = $this->get('open_orchestra_user.repository.user');

        if ($this->get('security.authorization_checker')->isGranted(ContributionRoleInterface::PLATFORM_ADMIN)) {
            $collection = $repository->findForPaginate($configuration);
            $recordsTotal = $repository->count();
            $recordsFiltered = $repository->countWithFilter($configuration);
        } else {
            $sitesId = $this->getAvailableSiteIds();

            $collection = $repository->findForPaginateFilterBySiteIds($configuration, $sitesId);
            $recordsTotal = $repository->countFilterBySiteIds($sitesId);
            $recordsFiltered = $repository->countWithFilterAndSiteIds($configuration, $sitesId);
        }

        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('user_collection');
        $facade = $collectionTransformer->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }

    /**
     * @param Request $request
     * @param string  $groupId
     *
     * @Config\Route("/members-group/{groupId}", name="open_orchestra_api_user_list_in_group")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function listInGroupAction(Request $request, $groupId)
    {
        $mapping = array(
            'username' => 'username',
            'email' => 'email'
        );
        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);
        $repository = $this->get('open_orchestra_user.repository.user');

        $collection = $repository->findUsersByGroupsForPaginate($configuration, $groupId);
        $recordsTotal = $repository->countFilterByGroups($groupId);

        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('user_collection');
        $facade = $collectionTransformer->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsTotal;

        return $facade;
    }

    /**
     * @param Request $request
     * @param string  $groupId
     *
     * @Config\Route("/remove-group/{groupId}", name="open_orchestra_api_user_remove_group")
     * @Config\Method({"PUT", "GET"})
     *
     * @return FacadeInterface
     * @throws UserNotFoundHttpException
     */
    public function removeGroupAction(Request $request, $groupId)
    {
        $format = $request->get('_format', 'json');

        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            $this->getParameter('open_orchestra_user_admin.facade.user.class'),
            $format
        );
        $user = $this->get('open_orchestra_api.transformer_manager')->get('user')->reverseTransform($facade);
        if (!$user instanceof UserInterface) {
            throw new UserNotFoundHttpException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $user);

        $this->get('open_orchestra_user.repository.user')->removeGroup($user->getId(), $groupId);

        return array();
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/delete-multiple", name="open_orchestra_api_user_delete_multiple")
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
        $userRepository = $this->get('open_orchestra_user.repository.user');
        $users = $this->get('open_orchestra_api.transformer_manager')->get('user_collection')->reverseTransform($facade);
        $userIds = array();
        foreach ($users as $user) {
            if ($this->isGranted(ContributionActionInterface::DELETE, $user)) {
                $userIds[] = $user->getId();
                $this->dispatchEvent(UserEvents::USER_DELETE, new UserEvent($user));
            }
        }
        $userRepository->removeUsers($userIds);

        return array();
    }

    /**
     * @return array
     */
    protected function getAvailableSiteIds()
    {
        $sitesId = array();
        $availableSites = $this->get('open_orchestra_backoffice.context_manager')->getAvailableSites();
        foreach ($availableSites as $site) {
            $sitesId[] = $site->getId();
        }

        return $sitesId;
    }
}
