<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class ContentTypeController
 *
 * @Config\Route("content-type")
 */
class ContentTypeController extends Controller
{
    /**
     * @param string $contentTypeId
     *
     * @Config\Route("/{contentTypeId}", name="php_orchestra_content_type_show")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($contentTypeId)
    {
        $contentType = $this->get('php_orchestra_model.repository.content_type')->findOneByContentTypeId($contentTypeId);

        return $this->get('php_orchestra_api.transformer_manager')->get('content_type')->transform($contentType);
    }

    /**
     * @Config\Route("", name="php_orchestra_content_type_list")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $contentTypeCollection = $this->get('php_orchestra_model.repository.content_type')->findAll();

        return $this->get('php_orchestra_api.transformer_manager')->get('content_type_collection')->transform($contentTypeCollection);
    }
}
