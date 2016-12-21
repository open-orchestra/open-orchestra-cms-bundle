<?php

namespace OpenOrchestra\WorkflowAdminBundle\Controller\Api;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\DeleteStatusNotGrantedHttpException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\StatusEvent;
use OpenOrchestra\ModelInterface\StatusEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use OpenOrchestra\ApiBundle\Context\CMSGroupContext;

/**
 * Class StatusController
 *
 * @Config\Route("status")
 *
 * @Api\Serialize()
 */
class StatusController extends BaseController
{
    /**
     * @param Request $request
     *
     * @return FacadeInterface
     *
     * @Config\Route("", name="open_orchestra_api_status_list_table")
     * @Config\Method({"GET"})
     *
     * @Api\Groups({OpenOrchestra\ApiBundle\Context\CMSGroupContext::STATUS_LINKS})
     */
    public function listTableAction(Request $request)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, StatusInterface::ENTITY_TYPE);
        $mapping = array(
            'label' => 'labels'
        );
        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);
        $repository = $this->get('open_orchestra_model.repository.status');
        $collection = $repository->findForPaginate($configuration);
        $recordsTotal = $repository->count();
        $recordsFiltered = $repository->countWithFilter($configuration);
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('status_collection');
        $facade = $collectionTransformer->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }

    /**
     * @return FacadeInterface
     *
     * @Config\Route("/list", name="open_orchestra_api_status_list")
     * @Config\Method({"GET"})
     * @Config\Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     */
    public function listAction()
    {
        $status = $this->get('open_orchestra_model.repository.status')->findNotOutOfWorkflow();

        return $this->get('open_orchestra_api.transformer_manager')->get('status_collection')->transform($status);
    }

    /**
     * @param int $statusId
     *
     * @return Response
     * @throws DeleteStatusNotGrantedHttpException
     *
     * @Config\Route("/{statusId}/delete", name="open_orchestra_api_status_delete")
     * @Config\Method({"DELETE"})
     */
    public function deleteAction($statusId)
    {
        $status = $this->get('open_orchestra_model.repository.status')->find($statusId);

        $this->denyDeleteUnlessGranted($status);
        $this->get('event_dispatcher')->dispatch(StatusEvents::STATUS_DELETE, new StatusEvent($status));
        $this->get('object_manager')->remove($status);
        $this->get('object_manager')->flush();

        return array();
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/delete-multiple", name="open_orchestra_api_status_delete_multiple")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteStatusesAction(Request $request)
    {
        $format = $request->get('_format', 'json');

        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            $this->getParameter('open_orchestra_workflow_admin.facade.status_collection.class'),
            $format
        );
        $statusRepository = $this->get('open_orchestra_model.repository.status');
        $statuses = $this->get('open_orchestra_api.transformer_manager')->get('status_collection')->reverseTransform($facade);

        $statusIds = array();
        foreach ($statuses as $status) {
            $this->denyDeleteUnlessGranted($status);
            $statusIds[] = $status->getId();
            $this->dispatchEvent(StatusEvents::STATUS_DELETE, new StatusEvent($status));
        }
        $statusRepository->removeStatuses($statusIds);

        return array();
    }

    /**
     * Deny delete unless granted
     *
     * @param StatusInterface $status
     *
     * @throws AccessDeniedException
     * @return boolean
     */
    protected function denyDeleteUnlessGranted(StatusInterface $status)
    {
        if (!$this->isGranted(ContributionActionInterface::DELETE, $status)
            || $this->get('open_orchestra_backoffice.usage_finder.status')->hasUsage($status)
            || $status->isInitialState()
            || $status->isPublishedState()
            || $status->isTranslationState()
            || $status->isAutoPublishFromState()
            || $status->isAutoUnpublishToState()
        ) {
            throw $this->DeleteStatusNotGrantedHttpException($message);
        }
    }
}
