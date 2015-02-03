<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ModelInterface\Event\KeywordEvent;
use PHPOrchestra\ModelInterface\KeywordEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class KeywordController
 *
 * @Config\Route("keyword")
 */
class KeywordController extends BaseController
{
    /**
     * @param Request $request
     *
     * @Config\Route("/check", name="php_orchestra_api_check_keyword")
     * @Config\Method({"GET"})
     *
     * @return Response
     */
    public function checkAction(Request $request)
    {
        $keyword = $request->get('term');

        $suppressSpecialCharacter = $this->get('php_orchestra_backoffice.transformer.suppress_special_character');

        $keyword = $suppressSpecialCharacter->transform($keyword);

        return new JsonResponse(array('term' => $keyword), 200);
    }

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
        $this->dispatchEvent(KeywordEvents::KEYWORD_DELETE, new KeywordEvent($keyword));
        $dm->remove($keyword);
        $dm->flush();

        return new Response('', 200);
    }
}
