<?php

namespace OpenOrchestra\ApiBundle\Controller;

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
        $columns = $request->get('columns');
        $search = $request->get('search');
        $search = (null !== $search && isset($search['value'])) ? $search['value'] : null;
        $order = $request->get('order');
        $skip = $request->get('skip');
        $limit = $request->get('limit');

        $repository =  $this->get('open_orchestra_model.repository.site');

        $siteCollection = $repository->findByDeletedForPaginateAndSearch(false, $columns, $search, $order, $skip, $limit);
        $recordsTotal = $repository->countByDeleted(false);
        $recordFiltered = $repository->countByDeletedFilterSearch(false, $columns, $search);

        $facade = $this->get('open_orchestra_api.transformer_manager')->get('site_collection')->transform($siteCollection);
        $facade->setRecordsTotal($recordsTotal);
        $facade->setRecordFiltered($recordFiltered);

        return $facade;
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
