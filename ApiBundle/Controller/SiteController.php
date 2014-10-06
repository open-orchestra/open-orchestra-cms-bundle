<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SiteController
 *
 * @Config\Route("site")
 */
class SiteController extends Controller
{
    /**
     * @param string $siteId
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
        $site = $this->get('php_orchestra_model.repository.site')->findOneBy(array('siteId' => $siteId));

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

    /**
     * @param string $siteId
     *
     * @Config\Route("/{siteId}/delete", name="php_orchestra_api_site_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($siteId)
    {
        $site = $this->get('php_orchestra_model.repository.site')->findOneBy(array('siteId' => $siteId));
        $this->get('doctrine.odm.mongodb.document_manager')->remove($site);
        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response('', 200);
    }
}
