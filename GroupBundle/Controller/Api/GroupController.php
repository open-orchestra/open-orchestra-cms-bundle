<?php

namespace OpenOrchestra\GroupBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\UserBundle\Event\GroupEvent;
use OpenOrchestra\UserBundle\GroupEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Model\GroupInterface;

/**
 * Class GroupController
 *
 * @Config\Route("group")
 *
 * @Api\Serialize()
 */
class GroupController extends BaseController
{
    /**
     * @param Request $request
     *
     * @Config\Route("/list", name="open_orchestra_api_group_list", defaults={"withCount" = true})
     * @Config\Route("/user/list", name="open_orchestra_api_group_user_list", defaults={"withCount" = false})
     *
     * @Config\Method({"GET"})
     *
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::SITE
     * })
     * @return FacadeInterface
     */
    public function listAction(Request $request, $withCount)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, GroupInterface::ENTITY_TYPE);
        $siteIds = array();
        $availableSites = $this->get('open_orchestra_backoffice.context_manager')->getAvailableSites();
        foreach ($availableSites as $site) {
            if ($this->isGranted(ContributionActionInterface::READ, $site)) {
                $siteIds[] = $site->getId();
            }
        }
        $mapping = array(
            'label' => 'labels',
        );

        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);
        $repository = $this->get('open_orchestra_user.repository.group');
        $collection = $repository->findForPaginate($configuration, $siteIds);

        $nbrGroupsUsers = array();
        if ($withCount) {
            $filter = $collection;
            array_walk($filter, function(&$item) {$item = $item->getId();});
            $nbrGroupsUsers = $this->get('open_orchestra_user.repository.user')->getCountsUsersByGroups($filter);
        }

        $recordsTotal = $repository->count($siteIds);
        $recordsFiltered = $repository->countWithFilter($configuration, $siteIds);

        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('group_collection');
        $facade = $collectionTransformer->transform($collection, $nbrGroupsUsers);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/delete-multiple", name="open_orchestra_api_group_delete_multiple")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteGroupsAction(Request $request)
    {
        $format = $request->get('_format', 'json');
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            $this->getParameter('open_orchestra_group.facade.group_collection.class'),
            $format
        );
        $groupRepository = $this->get('open_orchestra_user.repository.group');
        $groups = $this->get('open_orchestra_api.transformer_manager')->get('group_collection')->reverseTransform($facade);
        $groupIds = array();
        foreach ($groups as $group) {
            if ($this->isGranted(ContributionActionInterface::DELETE, $group)) {
                $groupIds[] = $group->getId();
                $this->dispatchEvent(GroupEvents::GROUP_DELETE, new GroupEvent($group));
            }
        }

        $groupRepository->removeGroups($groupIds);

        return array();
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/duplicate", name="open_orchestra_api_group_duplicate")
     * @Config\Method({"POST"})
     *
     * @return Response
     *
     * @throws AccessDeniedException
     */
    public function duplicateAction(Request $request)
    {
        $format = $request->get('_format', 'json');
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            $this->getParameter('open_orchestra_group.facade.group.class'),
            $format
        );

        $group = $this->get('open_orchestra_api.transformer_manager')->get('group')->reverseTransform($facade);

        $newGroup = clone $group;

        $currentSiteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $currentSite = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($currentSiteId);

        $newGroup->setWorkflowProfileCollections($group->getWorkflowProfileCollections());
        if ($newGroup->getSite()->getId() === $currentSite->getId()) {
            $newGroup->setPerimeters($group->getPerimeters());
        }
        $newGroup->setSite($currentSite);

        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, GroupInterface::ENTITY_TYPE);

        $objectManager = $this->get('object_manager');
        $objectManager->persist($newGroup);
        $objectManager->flush();

        return array();
    }
}
