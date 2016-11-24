<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\KeywordNotDeletableException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\KeywordEvent;
use OpenOrchestra\ModelInterface\KeywordEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class KeywordController
 *
 * @Config\Route("keyword")
 *
 * @Api\Serialize()
 */
class KeywordController extends BaseController
{
    use HandleRequestDataTable;

    /**
     * @param Request $request
     *
     * @Config\Route("/check", name="open_orchestra_api_check_keyword")
     * @Config\Method({"GET"})
     *
     * @return Response
     */
    public function checkAction(Request $request)
    {
        $keyword = $request->get('term');

        $suppressSpecialCharacter = $this->get('open_orchestra_model.helper.suppress_special_character');

        $keyword = $suppressSpecialCharacter->transform($keyword);

        return array('term' => $keyword);
    }

    /**
     * @param int $keywordId
     *
     * @Config\Route("/{keywordId}", name="open_orchestra_api_keyword_show")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function showAction($keywordId)
    {
        $keyword = $this->get('open_orchestra_model.repository.keyword')->find($keywordId);
        if ($keyword instanceof KeywordInterface) {
            $this->denyAccessUnlessGranted(ContributionActionInterface::READ, $keyword);

            return $this->get('open_orchestra_api.transformer_manager')->get('keyword')->transform($keyword);
        }

        return array();
    }

    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_keyword_list")
     * @Config\Method({"GET"})
     * @Config\Security("is_granted('ACCESS_KEYWORD')")
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $mapping = $this
            ->get('open_orchestra.annotation_search_reader')
            ->extractMapping($this->getParameter('open_orchestra_model.document.keyword.class'));

        $repository = $this->get('open_orchestra_model.repository.keyword');
        $collectionTransformer = $this->get('open_orchestra_api.transformer_manager')->get('keyword_collection');

        return $this->handleRequestDataTable($request, $repository, $mapping, $collectionTransformer);
    }

    /**
     * @param int $keywordId
     *
     * @Config\Route("/{keywordId}/delete", name="open_orchestra_api_keyword_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     * @throws KeywordNotDeletableException
     */
    public function deleteAction($keywordId)
    {
        $keyword = $this->get('open_orchestra_model.repository.keyword')->find($keywordId);

        if ($keyword instanceof KeywordInterface) {
            $this->denyAccessUnlessGranted(ContributionActionInterface::DELETE, $keyword);
            if ($keyword->isUsed()) {
                throw new KeywordNotDeletableException();
            }
            $dm = $this->get('object_manager');
            $this->dispatchEvent(KeywordEvents::KEYWORD_DELETE, new KeywordEvent($keyword));
            $dm->remove($keyword);
            $dm->flush();
        }

        return array();
    }
}
