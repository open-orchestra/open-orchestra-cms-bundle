<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\ModelInterface\Model\SoftDeleteableInterface;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\ModelInterface\Model\TrashItemInterface;

/**
 * Class TrashcanController
 *
 * @Config\Route("trashcan")
 *
 * @Api\Serialize()
 */
class TrashcanController extends BaseController
{
    /**
     * @param Request $request
     *
     * @Config\Route("/list", name="open_orchestra_api_trashcan_list")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $mapping = array(
            'name' => 'name',
            'type' => 'type',
            'deleted_at' => 'deletedAt'
        );

        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);
        $repository = $this->get('open_orchestra_model.repository.trash_item');
        $collection = $repository->findForPaginate($configuration, $siteId);
        $recordsTotal = $repository->countBySite($siteId);
        $recordsFiltered = $repository->countWithFilter($configuration, $siteId);
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('trash_item_collection');
        $facade = $collectionTransformer->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
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

        if ($trashItem instanceof TrashItemInterface) {
            /* @var $entity SoftDeleteableInterface */
            $entity = $trashItem->getEntity();
            $om = $this->get('object_manager');

            if ($this->isValid($entity, 'restore')) {
                $this->get('open_orchestra_backoffice.restore_entity.manager')->restore($entity);
                $om->remove($trashItem);
                $om->flush();

                return array();
            }

            return $this->getViolations();
        }

        return array();
    }


    /**
     * @param Request $request
     *
     * @Config\Route("/delete-multiple", name="open_orchestra_api_trashcan_delete_multiple")
     * @Config\Method({"DELETE"})
     *
     * @return array|mixed
     */
    public function deleteTrashitemsAction(Request $request)
    {
        $format = $request->get('_format', 'json');
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            $this->getParameter('open_orchestra_api.facade.trash_item_collection.class'),
            $format
        );
        $trashItems = $this->get('open_orchestra_api.transformer_manager')->get('trash_item_collection')->reverseTransform($facade);
        $trashItemIds = array();

        foreach ($trashItems as $trashItem) {
            //if ($this->isValid($trashItem, 'remove')) {
                $trashItemIds[] = $trashItem->getId();
                $this->get('open_orchestra_backoffice.remove_trashcan_entity.manager')->remove($trashItem);
            //}
        }

        $this->get('open_orchestra_model.repository.trash_item')->removeTrashItems($trashItemIds);
        $this->get('object_manager')->flush();

        return array();
    }
}
