<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class KeywordController
 *
 * @Config\Route("keyword")
 */
class KeywordController extends Controller
{
    /**
     * @param int $keywordId
     *
     * @Config\Route("/{keywordId}", name="php_orchestra_api_keyword_show")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($keywordId)
    {
        $keyword = $this->get('php_orchestra_model.repository.keyword')->find($keywordId);

        return $this->get('php_orchestra_api.transformer_manager')->get('keyword')->transform($keyword);
    }

    /**
     * @Config\Route("", name="php_orchestra_api_keyword_list")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $keywordCollection = $this->get('php_orchestra_model.repository.keyword')->findAll();

        return $this->get('php_orchestra_api.transformer_manager')->get('keyword_collection')->transform($keywordCollection);
    }

    /**
     * @param int $keywordId
     *
     * @Config\Route("/{keywordId}/delete", name="php_orchestra_api_keyword_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($keywordId)
    {
        $keyword = $this->get('php_orchestra_model.repository.keyword')->find($keywordId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->remove($keyword);
        $dm->flush();

        return new Response('', 200);
    }
}
