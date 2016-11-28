<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\DeleteStatusNotGrantedHttpException;
use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\StatusEvent;
use OpenOrchestra\ModelInterface\StatusEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
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
    use HandleRequestDataTable;

    /**
     * @param Request $request
     *
     * @return FacadeInterface
     *
     * @Config\Route("", name="open_orchestra_api_status_list_table")
     * @Config\Method({"GET"})
     *
     * @Api\Groups({CMSGroupContext::STATUS_LINKS})
     */
    public function listTableAction(Request $request)
    {
        $mapping = $this
            ->get('open_orchestra.annotation_search_reader')
            ->extractMapping($this->container->getParameter('open_orchestra_model.document.status.class'));
        $repository = $this->get('open_orchestra_model.repository.status');
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('status_collection');

        return $this->handleRequestDataTable($request, $repository, $mapping, $collectionTransformer);
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
        if (!$this->isGranted(AdministrationPanelStrategy::ROLE_ACCESS_DELETE_STATUS, $status)
            || $this->get('open_orchestra_backoffice.usage_finder.status')->hasUsage($status)
        ) {
            throw new DeleteStatusNotGrantedHttpException();
        }
        $this->get('event_dispatcher')->dispatch(StatusEvents::STATUS_DELETE, new StatusEvent($status));
        $this->get('object_manager')->remove($status);
        $this->get('object_manager')->flush();

        return array();
    }
}
