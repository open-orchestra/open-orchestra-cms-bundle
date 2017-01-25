<?php

namespace OpenOrchestra\WorkflowAdminBundle\Controller\Api;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\WorkflowProfileEvent;
use OpenOrchestra\ModelInterface\Model\WorkflowProfileInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\ModelInterface\WorkflowProfileEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WorkflowProfileController
 *
 * @Config\Route("workflow-profile")
 *
 * @Api\Serialize()
 */
class WorkflowProfileController extends BaseController
{
    /**
     * @param Request $request
     *
     * @return FacadeInterface
     *
     * @Config\Route("", name="open_orchestra_api_workflow_profile_list")
     * @Config\Method({"GET"})
     * @Api\Groups({\OpenOrchestra\ApiBundle\Context\CMSGroupContext::AUTHORIZATIONS})
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, WorkflowProfileInterface::ENTITY_TYPE);
        $mapping = array(
            'label' => 'labels'
        );
        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);
        $repository = $this->get('open_orchestra_model.repository.workflow_profile');
        $collection = $repository->findForPaginate($configuration);
        $recordsTotal = $repository->count();
        $recordsFiltered = $repository->countWithFilter($configuration);
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('workflow_profile_collection');
        $facade = $collectionTransformer->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/delete-multiple", name="open_orchestra_api_workflow_profile_delete_multiple")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteWorkflowProfilesAction(Request $request)
    {
        $format = $request->get('_format', 'json');
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            $this->getParameter('open_orchestra_workflow_admin.facade.workflow_profile_collection.class'),
            $format
        );
        $workflowProfiles = $this->get('open_orchestra_api.transformer_manager')->get('workflow_profile_collection')->reverseTransform($facade);
        $workflowProfileIds = array();
        foreach ($workflowProfiles as $workflowProfile) {
            if ($this->isGranted(ContributionActionInterface::DELETE, $workflowProfile)) {
                $workflowProfileIds[] = $workflowProfile->getId();
                $this->dispatchEvent(WorkflowProfileEvents::WORKFLOW_PROFILE_DELETE, new WorkflowProfileEvent($workflowProfile));
            }
        }

        $workflowProfileRepository = $this->get('open_orchestra_model.repository.workflow_profile');
        $workflowProfileRepository->removeWorkflowProfiles($workflowProfileIds);

        return array();
    }

    /**
     * @param string $workflowProfileId
     *
     * @Config\Route("/{workflowProfileId}/delete", name="open_orchestra_api_workflow_profile_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($workflowProfileId)
    {
        $workflowProfile = $this->get('open_orchestra_model.repository.workflow_profile')->find($workflowProfileId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::DELETE, $workflowProfile);

        if ($workflowProfile instanceof WorkflowProfileInterface) {
            $objectManager = $this->get('object_manager');
            $objectManager->remove($workflowProfile);
            $objectManager->flush();
            $this->dispatchEvent(WorkflowProfileEvents::WORKFLOW_PROFILE_DELETE, new WorkflowProfileEvent($workflowProfile));
        }

        return array();
    }
}
