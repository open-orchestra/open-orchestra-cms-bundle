<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;
use OpenOrchestra\ModelInterface\Model\SoftDeleteableInterface;

/**
 * Class TrashcanController
 *
 * @Config\Route("trashcan")
 *
 * @Api\Serialize()
 */
class TrashcanController extends BaseController
{
    use HandleRequestDataTable;

    /**
     * @param Request $request
     *
     * @Config\Route("/list", name="open_orchestra_api_trashcan_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_DELETED')")
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        $mapping = $this->get('open_orchestra.annotation_search_reader')->extractMapping($this->getParameter('open_orchestra_model.document.trash_item.class'));
        $repository = $this->get('open_orchestra_model.repository.trash_item');
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('trash_item_collection');

        return $this->handleRequestDataTable($request, $repository, $mapping, $collectionTransformer);
    }


    /**
     * @param $trashItemId
     *
     * @Config\Route("/{trashItemId}/restore", name="open_orchestra_api_trashcan_restore")
     * @Config\Method({"PUT"})
     *
     * @return array|mixed
     */
    public function restoreAction($trashItemId)
    {
        /* @var TrashItemInterface $trashItem */
        $trashItem = $this->get('open_orchestra_model.repository.trash_item')->find($trashItemId);
        /* @var $entity SoftDeleteableInterface */
        $entity = $trashItem->getEntity();
        $dm = $this->get('object_manager');

        if ($this->isValid($entity, 'restore')) {
            $this->get('open_orchestra_backoffice.restore_entity.manager')->restore($entity);
            $dm->remove($trashItem);
            $dm->flush();

            return array();
        }

        return $this->getViolations();
    }
}
