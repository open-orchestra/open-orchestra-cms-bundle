<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\SiteEvent;
use OpenOrchestra\ModelInterface\SiteEvents;
use OpenOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

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
     * @Config\Security("has_role('ROLE_PANEL_SITE')")
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
     * @Config\Route("", name="open_orchestra_api_site_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_PANEL_SITE')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $siteCollection = $this->get('open_orchestra_model.repository.site')->findByDeleted(false);

        return $this->get('open_orchestra_api.transformer_manager')->get('site_collection')->transform($siteCollection);
    }

    /**
     * @param string $siteId
     *
     * @Config\Route("/{siteId}/delete", name="open_orchestra_api_site_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_PANEL_SITE')")
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
