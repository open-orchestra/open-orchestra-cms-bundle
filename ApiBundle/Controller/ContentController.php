<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @param Request $request
     *
     * @Config\Route("", name="php_orchestra_api_content_list")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $criteria = array('deleted' => false);
        if ($contentType = $request->get('content_type')) {
            $criteria['contentType'] = $contentType;
        }

        $contentCollection = $this->get('php_orchestra_model.repository.content')->findBy($criteria);

        return $this->get('php_orchestra_api.transformer_manager')->get('content_collection')->transform($contentCollection);
    }

    /**
     * @param string $contentId
     *
     * @Config\Route("/{contentId}/delete", name="php_orchestra_api_content_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($contentId)
    {
        $content = $this->get('php_orchestra_model.repository.content')->findOneByContentId($contentId);
        $content->setDeleted(true);

        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response('', 200);
    }
}
