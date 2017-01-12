<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
use OpenOrchestra\ApiBundle\Controller\ControllerTrait\ListStatus;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\ContentNotDeletableException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\ContentNotFoundHttpException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\SourceLanguageNotFoundHttpException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\Pagination\Configuration\PaginateFinderConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;
use OpenOrchestra\BaseApi\Context\GroupContext;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\ModelInterface\Model\SiteInterface;

/**
 * Class ContentController
 *
 * @Config\Route("content")
 *
 * @Api\Serialize()
 */
class ContentController extends BaseController
{
    use ListStatus;
    use HandleRequestDataTable;

    /**
     * @param Request $request
     * @param string  $contentId
     *
     * @Config\Route("/{contentId}", name="open_orchestra_api_content_show")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     * @throws ContentNotFoundHttpException
     */
    public function showAction(Request $request, $contentId)
    {
        $content = $this->findOneContent($contentId, $request->get('language'), $request->get('version'));

        if (!$content) {
            throw new ContentNotFoundHttpException();
        }

        return $this->get('open_orchestra_api.transformer_manager')->get('content')->transform($content);
    }

    /**
     * @param Request $request
     * @param string  $contentId
     *
     * @Config\Route("/{contentId}/show-or-create", name="open_orchestra_api_content_show_or_create")
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     * @throws SourceLanguageNotFoundHttpException
     */
    public function showOrCreateAction(Request $request, $contentId)
    {
        $content = $this->showOrCreate($request, $contentId);

        return $this->get('open_orchestra_api.transformer_manager')->get('content')->transform($content);
    }

    /**
     * @param Request $request
     * @param string  $contentId
     *
     * @return ContentInterface
     * @throws SourceLanguageNotFoundHttpException
     */
    protected function showOrCreate(Request $request, $contentId)
    {
        $language = $request->get('language');
        $content = $this->findOneContent($contentId, $language, $request->get('version'));

        if (!$content) {
            $sourceLanguage = $request->get('source_language');
            if (!$sourceLanguage) {
                throw new SourceLanguageNotFoundHttpException();
            }
            $oldContent = $this->findOneContent($contentId, $sourceLanguage);
            $content = $this->get('open_orchestra_backoffice.manager.content')->createNewLanguageContent($oldContent, $language);
            $dm = $this->get('object_manager');
            $dm->persist($content);
            $dm->flush($content);
        }

        return $content;
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/list/{contentType}/{siteId}/{language}", name="open_orchestra_api_content_list")
     * @Config\Method({"GET"})
     *
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::FIELD_TYPES
     * })
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request, $contentType, $siteId, $language)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, SiteInterface::ENTITY_TYPE);
        $mapping = array();
        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);
        $repository =  $this->get('open_orchestra_model.repository.content');

        $collection = $repository->findForPaginateFilterByContentTypeSiteAndLanguage($configuration, $contentType, $siteId, $language);
        $recordsTotal = $repository->countFilterByContentTypeSiteAndLanguage($contentType, $siteId, $language);
        $recordsFiltered = $repository->countWithFilterAndContentTypeSiteAndLanguage($configuration, $contentType, $siteId, $language);
        $facade = $this->get('open_orchestra_api.transformer_manager')->get('content_collection')->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }

    /**
     * @param string $contentId
     *
     * @Config\Route("/{contentId}/delete", name="open_orchestra_api_content_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     * @throws ContentNotDeletableException
     */
    public function deleteAction($contentId)
    {
        $content = $this->get('open_orchestra_model.repository.content')->find($contentId);

        if ($content instanceof ContentInterface) {
            if ($content->isUsed()) {
                throw new ContentNotDeletableException();
            }

            $content->setDeleted(true);
            $this->get('object_manager')->flush();
            $this->dispatchEvent(ContentEvents::CONTENT_DELETE, new ContentEvent($content));
        }

        return array();
    }

    /**
     * @param Request $request
     * @param string  $contentId
     *
     * @Config\Route("/{contentId}/new-version", name="open_orchestra_api_content_new_version")
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function newVersionAction(Request $request, $contentId)
    {
        /** @var ContentInterface $content */
        $content = $this->findOneContent($contentId, $request->get('language'), $request->get('version'));
        $lastContent = $this->findOneContent($contentId, $request->get('language'));
        $newContent = $this->get('open_orchestra_backoffice.manager.content')->newVersionContent($content, $lastContent);

        $this->dispatchEvent(ContentEvents::CONTENT_DUPLICATE, new ContentEvent($newContent));

        return array();
    }

    /**
     * @param string  $contentId
     *
     * @Config\Route("/{contentId}/duplicate", name="open_orchestra_api_content_duplicate")
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function duplicateAction($contentId)
    {
        $frontLanguages = $this->getParameter('open_orchestra_backoffice.orchestra_choice.front_language');
        $newContentId = null;
        foreach (array_keys($frontLanguages) as $language) {
            $content = $this->findOneContent($contentId, $language);
            if ($content instanceof ContentInterface) {
                $duplicateContent = $this->get('open_orchestra_backoffice.manager.content')->duplicateContent($content, $newContentId);
                $newContentId = $duplicateContent->getContentId();
                $this->dispatchEvent(ContentEvents::CONTENT_DUPLICATE, new ContentEvent($duplicateContent));
            }
        }

        return array();
    }

    /**
     * @param Request $request
     * @param string  $contentId
     *
     * @Config\Route("/{contentId}/list-version", name="open_orchestra_api_content_list_version")
     * @Config\Method({"GET"})
     *
     * @return Response
     */
    public function listVersionAction(Request $request, $contentId)
    {
        $contents = $this->get('open_orchestra_model.repository.content')->findByLanguage($contentId, $request->get('language'));

        return $this->get('open_orchestra_api.transformer_manager')->get('content_collection')->transform($contents);
    }

    /**
     * @param Request $request
     * @param string $contentMongoId
     *
     * @Config\Route("/{contentMongoId}/update", name="open_orchestra_api_content_update")
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function updateAction(Request $request, $contentMongoId)
    {
        return $this->reverseTransform(
            $request,
            $contentMongoId,
            'content',
            ContentEvents::CONTENT_CHANGE_STATUS,
            'OpenOrchestra\ModelInterface\Event\ContentEvent'
        );
    }

    /**
     * @param string $contentMongoId
     *
     * @Config\Route("/{contentMongoId}/list-statuses", name="open_orchestra_api_content_list_status")
     * @Config\Method({"GET"})
     *
     * @return Response
     */
    public function listStatusesForContentAction($contentMongoId)
    {
        $content = $this->get('open_orchestra_model.repository.content')->find($contentMongoId);

        return $this->listStatuses($content);
    }

    /**
     * @param string   $contentId
     * @param string   $language
     * @param int|null $version
     *
     * @return null|ContentInterface
     */
    protected function findOneContent($contentId, $language, $version = null)
    {
        $contentRepository = $this->get('open_orchestra_model.repository.content');
        $content = $contentRepository->findOneByLanguageAndVersion($contentId, $language, $version);

        return $content;
    }

    /**
     * @param boolean|null $published
     *
     * @Config\Route("/list/not-published-by-author", name="open_orchestra_api_content_list_author_and_site_not_published", defaults={"published": false})
     * @Config\Route("/list/by-author", name="open_orchestra_api_content_list_author_and_site", defaults={"published": null})
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     */
    public function listContentByAuthorAndSiteIdAction($published)
    {
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $content = $this->get('open_orchestra_model.repository.content')->findByHistoryAndSiteId(
            $user->getId(),
            $siteId,
            array(ContentEvents::CONTENT_CREATION, ContentEvents::CONTENT_UPDATE),
            $published,
            10,
            array('histories.updatedAt' => -1)
        );

        return $this->get('open_orchestra_api.transformer_manager')->get('content_collection')->transform($content);
    }
}
