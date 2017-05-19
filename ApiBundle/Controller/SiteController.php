<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\SiteEvent;
use OpenOrchestra\ModelInterface\SiteEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\ModelInterface\Model\SiteInterface;

/**
 * Class SiteController
 *
 * @Config\Route("site")
 *
 * @Api\Serialize()
 */
class SiteController extends BaseController
{
    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_site_list")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, SiteInterface::ENTITY_TYPE);
        $mapping = array(
            'name' => 'name'
        );
        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);
        $repository =  $this->get('open_orchestra_model.repository.site');

        $siteIds = null;
        if (false === $this->get('security.authorization_checker')->isGranted(ContributionRoleInterface::PLATFORM_ADMIN)) {
            $siteIds = array();
            $availableSites = $this->get('open_orchestra_backoffice.context_backoffice_manager')->getAvailableSites();
            foreach ($availableSites as $site) {
                $siteIds[] = $site->getSiteId();
            }
        }

        $collection = $repository->findForPaginateFilterBySiteIds($configuration, $siteIds);
        $recordsTotal = $repository->countFilterBySiteIds($siteIds);
        $recordsFiltered = $repository->countWithFilterAndSiteIds($configuration, $siteIds);
        $facade = $this->get('open_orchestra_api.transformer_manager')->get('site_collection')->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }

    /**
     * @Config\Route("/list/available", name="open_orchestra_api_available_site_list")
     * @Config\Method({"GET"})
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::SITE_MAIN_ALIAS
     * })
     *
     * @return FacadeInterface
     */
    public function listAvailableSiteAction()
    {
        $availableSite = $this->get('open_orchestra_backoffice.context_backoffice_manager')->getAvailableSites();

        return $this->get('open_orchestra_api.transformer_manager')->get('site_collection')->transform($availableSite);
    }

    /**
     * @param string $siteId
     *
     * @Config\Route("/{siteId}/delete", name="open_orchestra_api_site_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($siteId)
    {
        $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::DELETE, $site);

        if ($site instanceof SiteInterface) {
            $site->setDeleted(true);
            $this->dispatchEvent(SiteEvents::SITE_DELETE, new SiteEvent($site));
            $this->get('object_manager')->flush();
        }

        return array();
    }
}
