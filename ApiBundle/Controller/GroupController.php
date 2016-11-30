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
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;

/**
 * Class GroupController
 *
 * @Config\Route("group")
 *
 * @Api\Serialize()
 */
class GroupController extends BaseController
{
    use HandleRequestDataTable;

    /**
     * @param string $groupId
     *
     * @return FacadeInterface
     *
     * @Config\Route("/{groupId}", name="open_orchestra_api_group_show")
     * @Config\Method({"GET"})
     *
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::GROUP_ROLES,
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::SITE
     * })
     */
    public function showAction($groupId)
    {
        $group = $this->get('open_orchestra_user.repository.group')->find($groupId);

        return $this->get('open_orchestra_api.transformer_manager')->get('group')->transform($group);
    }

    /**
     * @param Request $request
     * @param string  $groupId
     *
     * @Config\Route("/{groupId}", name="open_orchestra_api_group_edit")
     * @Config\Method({"POST"})
     *
     * @return array
     */
    public function editAction(Request $request, $groupId)
    {
        $facade = $this->get('jms_serializer')->deserialize($request->getContent(), 'OpenOrchestra\ApiBundle\Facade\GroupFacade', $request->get('_format', 'json'));
        $group = $this->get('open_orchestra_user.repository.group')->find($groupId);

        $this->get('open_orchestra_api.transformer_manager')->get('group')->reverseTransform($facade, $group);

        $this->get('object_manager')->flush();

        return array();
    }

    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_group_list")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $mapping = $this
            ->get('open_orchestra.annotation_search_reader')
            ->extractMapping($this->container->getParameter('open_orchestra_user.document.group.class'));
        $repository = $this->get('open_orchestra_user.repository.group');
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('group_collection');

        return $this->handleRequestDataTable($request, $repository, $mapping, $collectionTransformer);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/user/group", name="open_orchestra_api_group_user_list")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function listUserAction(Request $request)
    {
        $siteIds = null;
        if (!$this->getUser()->hasRole(ContributionRoleInterface::PLATFORM_ADMIN) &&
            $this->getUser()->hasRole(ContributionRoleInterface::DEVELOPER)
        ) {
            foreach ($this->getUser()->getGroups() as $group) {
                $site = $group->getSite();
                if (!$site->isDeleted() && !in_array($site->getSiteId(), $siteIds)) {
                    $siteIds[] = $site->getSiteId();
                }
            }
        }
        $groups = $this->get('open_orchestra_user.repository.group')->findBySiteId($siteIds);

        return $this->get('open_orchestra_api.transformer_manager')->get('group_collection')->transform($groups);
    }

    /**
     * @param string $groupId
     *
     * @Config\Route("/{groupId}/delete", name="open_orchestra_api_group_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($groupId)
    {
        $group = $this->get('open_orchestra_user.repository.group')->find($groupId);
        $dm = $this->get('object_manager');
        $this->dispatchEvent(GroupEvents::GROUP_DELETE, new GroupEvent($group));
        $dm->remove($group);
        $dm->flush();

        return array();
    }

    /**
     * @param string $groupId
     *
     * @Config\Route("/{groupId}/duplicate", name="open_orchestra_api_group_duplicate")
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function duplicateAction($groupId)
    {
        $group = $this->get('open_orchestra_user.repository.group')->find($groupId);

        $newGroup = clone $group;

        if (! $this->isValid($newGroup)) {
            return $this->getViolations();
        }

        $objectManager = $this->get('object_manager');
        $objectManager->persist($newGroup);
        $objectManager->flush();

        return array();
    }
}
