<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

/**
 * Class ContentController
 *
 * @Config\Route("content")
 */
class ContentController extends Controller
{
    /**
     * @param string $contentId
     *
     * @Config\Route("/{contentId}", name="php_orchestra_api_content_show")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($contentId)
    {
        $content = $this->get('php_orchestra_model.repository.content')->findOneByContentId($contentId);

        return $this->get('php_orchestra_api.transformer_manager')->get('content')->transform($content);
    }

    /**
     * @Config\Route("", name="php_orchestra_api_content_list")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $contentCollection = $this->get('php_orchestra_model.repository.content')->findAll();

        return $this->get('php_orchestra_api.transformer_manager')->get('content_collection')->transform($contentCollection);
    }
}
