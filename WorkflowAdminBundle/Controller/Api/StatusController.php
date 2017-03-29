<?php

namespace OpenOrchestra\WorkflowAdminBundle\Controller\Api;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\StatusEvent;
use OpenOrchestra\ModelInterface\StatusEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\WorkflowAdminBundle\Exceptions\HttpException\StatusNotDeletableException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;

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
     * @Api\Groups({\OpenOrchestra\ApiBundle\Context\CMSGroupContext::AUTHORIZATIONS})
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
        $recordsTotal = $repository->countNotOutOfWorkflow();
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
            if ($this->isDeleteGranted($status)) {
                $statusIds[] = $status->getId();
                $this->dispatchEvent(StatusEvents::STATUS_DELETE, new StatusEvent($status));
            }
        }
        $statusRepository->removeStatuses($statusIds);

        return array();
    }

    /**
     * @param string $statusId
     *
     * @Config\Route("/{statusId}/delete", name="open_orchestra_api_status_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     * @throws StatusNotDeletableException
     */
    public function deleteAction($statusId)
    {
        $status = $this->get('open_orchestra_model.repository.status')->find($statusId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::DELETE, $status);

        if (!$this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(ContributionActionInterface::DELETE, $status)) {
            throw new StatusNotDeletableException();
        }

        $objectManager = $this->get('object_manager');
        $objectManager->remove($status);
        $objectManager->flush();
        $this->dispatchEvent(StatusEvents::STATUS_DELETE, new StatusEvent($status));

        return array();
    }

    /**
     * Check if current user can delete $status
     *
     * @param StatusInterface $status
     *
     * @return boolean
     */
    protected function isDeleteGranted(StatusInterface $status)
    {
        return $this->isGranted(ContributionActionInterface::DELETE, $status)
            && $this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(ContributionActionInterface::DELETE, $status);
    }
}
