<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class SiteController
 *
 * @Config\Route("site")
 */
class SiteController extends Controller
{
    /**
     * @param int $siteId
     *
     * @Config\Route("/{siteId}", name="php_orchestra_api_site_show")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($siteId)
    {
        $site = $this->get('php_orchestra_model.repository.site')->findOneBy(array('siteId' => (int) $siteId));

        return $this->get('php_orchestra_api.transformer_manager')->get('site')->transform($site);
    }

    /**
     * @Config\Route("", name="php_orchestra_api_site_list")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $siteCollection = $this->get('php_orchestra_model.repository.site')->findAll();

        return $this->get('php_orchestra_api.transformer_manager')->get('site_collection')->transform($siteCollection);
    }
}
