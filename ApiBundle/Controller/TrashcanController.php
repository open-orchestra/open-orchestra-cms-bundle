<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
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
    use HandleRequestDataTable;

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

        $mapping = $this->get('open_orchestra.annotation_search_reader')->extractMapping($this->getParameter('open_orchestra_model.document.trash_item.class'));
        $repository = $this->get('open_orchestra_model.repository.trash_item');
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('trash_item_collection');

        return $this->handleRequestDataTable($request, $repository, $mapping, $collectionTransformer, array("site_id" => $siteId));
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
     * @param $trashItemId
     *
     * @Config\Route("/{trashItemId}/remove", name="open_orchestra_api_trashcan_remove")
     * @Config\Method({"DELETE"})
     *
     * @return array|mixed
     */
    public function removeAction($trashItemId)
    {
        /* @var TrashItemInterface $trashItem */
        $trashItem = $this->get('open_orchestra_model.repository.trash_item')->find($trashItemId);
        if ($this->isValid($trashItem, 'remove')) {
            /* @var $entity SoftDeleteableInterface */
            $entity = $trashItem->getEntity();
            $om = $this->get('object_manager');
            $om->remove($trashItem);
            $om->flush($trashItem);

            $this->get('open_orchestra_backoffice.remove_trashcan_entity.manager')->remove($entity);

            return array();
        }

        return $this->getViolations();
    }
}
