<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\Backoffice\BusinessRules\Strategies\BusinessActionInterface;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\Event\KeywordEvent;
use OpenOrchestra\ModelInterface\KeywordEvents;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
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
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_keyword_list")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, KeywordInterface::ENTITY_TYPE);
        $mapping = array(
            'label' => 'label'
        );
        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);
        $repository = $this->get('open_orchestra_model.repository.keyword');
        $collection = $repository->findForPaginate($configuration);
        $recordsTotal = $repository->count();
        $recordsFiltered = $repository->countWithFilter($configuration);
        $facade = $this->get('open_orchestra_api.transformer_manager')->transform('keyword_collection', $collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/delete-multiple", name="open_orchestra_api_keyword_delete_multiple")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteKeywordsAction(Request $request)
    {
        $format = $request->get('_format', 'json');
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            $this->getParameter('open_orchestra_api.facade.keyword_collection.class'),
            $format
        );
        $keywords = $this->get('open_orchestra_api.transformer_manager')->reverseTransform('keyword_collection', $facade);
        $keywordIds = array();
        foreach ($keywords as $keyword) {
            if ($this->isGranted(ContributionActionInterface::DELETE, $keyword) &&
                $this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(BusinessActionInterface::DELETE, $keyword)
            ) {
                $keywordIds[] = $keyword->getId();
                $this->dispatchEvent(KeywordEvents::KEYWORD_DELETE, new KeywordEvent($keyword));
            }
        }

        $keywordRepository = $this->get('open_orchestra_model.repository.keyword');
        $keywordRepository->removeKeywords($keywordIds);

        return array();
    }
}
