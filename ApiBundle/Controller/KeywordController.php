<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\KeywordEvent;
use OpenOrchestra\ModelInterface\KeywordEvents;
use OpenOrchestra\ApiBundle\Controller\Annotation as Api;
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
     * @Config\Route("/check", name="open_orchestra_api_check_keyword")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_PANEL_KEYWORD')")
     *
     * @return Response
     */
    public function checkAction(Request $request)
    {
        $keyword = $request->get('term');

        $suppressSpecialCharacter = $this->get('open_orchestra_backoffice.transformer.suppress_special_character');

        $keyword = $suppressSpecialCharacter->transform($keyword);

        return new JsonResponse(array('term' => $keyword), 200);
    }

    /**
     * @param int $keywordId
     *
     * @Config\Route("/{keywordId}", name="open_orchestra_api_keyword_show")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_PANEL_KEYWORD')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($keywordId)
    {
        $keyword = $this->get('open_orchestra_model.repository.keyword')->find($keywordId);

        return $this->get('open_orchestra_api.transformer_manager')->get('keyword')->transform($keyword);
    }

    /**
     * @Config\Route("", name="open_orchestra_api_keyword_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_PANEL_KEYWORD')")
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction()
    {
        $keywordCollection = $this->get('open_orchestra_model.repository.keyword')->findAll();

        return $this->get('open_orchestra_api.transformer_manager')->get('keyword_collection')->transform($keywordCollection);
    }

    /**
     * @param int $keywordId
     *
     * @Config\Route("/{keywordId}/delete", name="open_orchestra_api_keyword_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_PANEL_KEYWORD')")
     *
     * @return Response
     */
    public function deleteAction($keywordId)
    {
        $keyword = $this->get('open_orchestra_model.repository.keyword')->find($keywordId);
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $this->dispatchEvent(KeywordEvents::KEYWORD_DELETE, new KeywordEvent($keyword));
        $dm->remove($keyword);
        $dm->flush();

        return new Response('', 200);
    }
}
