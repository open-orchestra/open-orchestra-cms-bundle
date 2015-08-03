<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\StatusEvent;
use OpenOrchestra\ModelInterface\StatusEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class StatusController
 *
 * @Config\Route("status")
 */
class StatusController extends BaseController
{
    use HandleRequestDataTable;

    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_status_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_STATUS')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $columnsNameToEntityAttribute = array(
            'label'     => array('key' => 'name'),
            'published' => array('key' => 'published','type' => 'boolean'),
            'initial' => array('key' => 'initial','type' => 'boolean'),
            'display_color' => array('key' => 'displayColor'),
        );
        $repository = $this->get('open_orchestra_model.repository.status');
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('status_collection');

        return $this->handleRequestDataTable($request, $repository, $columnsNameToEntityAttribute, $collectionTransformer);
    }

    /**
     * @param int $statusId
     *
     * @Config\Route("/{statusId}/delete", name="open_orchestra_api_status_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_STATUS')")
     *
     * @return Response
     */
    public function deleteAction($statusId)
    {
        $status = $this->get('open_orchestra_model.repository.status')->find($statusId);
        $this->get('event_dispatcher')->dispatch(StatusEvents::STATUS_DELETE, new StatusEvent($status));
        $this->get('object_manager')->remove($status);
        $this->get('object_manager')->flush();

        return new Response('', 200);
    }
}
