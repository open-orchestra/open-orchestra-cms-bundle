<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\SiteEvent;
use OpenOrchestra\ModelInterface\SiteEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class SiteController
 *
 * @Config\Route("site")
 */
class SiteController extends BaseController
{
    use HandleRequestDataTable;

    /**
     * @param string $siteId
     *
     * @Config\Route("/{siteId}", name="open_orchestra_api_site_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_SITE')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($siteId)
    {
        $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);

        return $this->get('open_orchestra_api.transformer_manager')->get('site')->transform($site);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_site_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_SITE')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        list($columns, $search, $order, $skip, $limit) = $this->extractParameterRequestDataTable($request);

        $columnsNameToEntityAttribute = array(
            'site_id' => array('key' => 'siteId'),
            'name'    => array('key' => 'name'),
        );

        $repository =  $this->get('open_orchestra_model.repository.site');
        $transformer = $this->get('open_orchestra_api.transformer_manager')->get('site_collection');

        if ($entityId = $request->get('entityId')) {
            $element = $repository->find($entityId);
            return $transformer->transform(array($element));
        }

        $siteCollection = $repository->findByDeletedForPaginateAndSearch(false, $columnsNameToEntityAttribute, $columns, $search, $order, $skip, $limit);
        $recordsTotal = $repository->countByDeleted(false);
        $recordsFiltered = $repository->countByDeletedWithSearchFilter(false, $columnsNameToEntityAttribute, $columns, $search);

        return $this->generateFacadeDataTable($transformer, $siteCollection, $recordsTotal, $recordsFiltered);
    }

    /**
     * @param string $siteId
     *
     * @Config\Route("/{siteId}/delete", name="open_orchestra_api_site_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_SITE')")
     *
     * @return Response
     */
    public function deleteAction($siteId)
    {
        $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);
        $site->setDeleted(true);
        $this->dispatchEvent(SiteEvents::SITE_DELETE, new SiteEvent($site));
        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response('', 200);
    }
}
