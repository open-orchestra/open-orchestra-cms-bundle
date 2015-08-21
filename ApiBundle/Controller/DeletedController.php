<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class DeletedController
 *
 * @Config\Route("deleted")
 *
 * @Api\Serialize()
 */
class DeletedController extends BaseController
{
    use HandleRequestDataTable;

    /**
     * @param Request $request
     *
     * @Config\Route("/list", name="open_orchestra_api_deleted_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_DELETED')")
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        $mapping = $this->get('open_orchestra_base.annotation_search_reader')->extractMapping('OpenOrchestra\ModelBundle\Document\TrashCan');
        $repository = $this->get('open_orchestra_model.repository.trash_can');
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('trashcan_collection');

        return $this->handleRequestDataTable($request, $repository, $mapping, $collectionTransformer);
    }
}
