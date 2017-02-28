<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\ListStatus;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\ContentNotFoundHttpException;
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

    /**
     * @param Request $request
     * @param string  $contentTypeId
     * @param string  $siteId
     * @param string  $language
     *
     * @Config\Route("/list/{contentTypeId}/{siteId}/{language}", name="open_orchestra_api_content_list")
     * @Config\Method({"GET"})
     *
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AUTHORIZATIONS
     * })
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request, $contentTypeId, $siteId, $language)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, SiteInterface::ENTITY_TYPE);

        $mapping = array(
            'name' => 'name',
            'status_label' => 'status.labels.'.$language,
            'linked_to_site' => 'linkedToSite',
            'created_at' => 'createdAt',
            'created_by' => 'createdBy',
            'updated_at' => 'updatedAt',
            'updated_by' => 'updatedBy',
        );
        $contentType = $this->get('open_orchestra_model.repository.content_type')->findOneByContentTypeIdInLastVersion($contentTypeId);
        foreach ($contentType->getDefaultListable() as $column => $isListable) {
            if (!$isListable) {
                unset($mapping[$column]);
            }
        }
        foreach ($contentType->getFields() as $field) {
            $mapping['fields.' . $field->getFieldId() . '.string_value'] = 'attributes.' .     $field->getFieldId() . '.stringValue';
        }

        $searchTypes = array();
        foreach ($contentType->getFields() as $field) {
            $searchTypes['attributes.' . $field->getFieldId()] = $field->getFieldTypeSearchable();
        }

        $configuration = PaginateFinderConfiguration::generateFromRequest($request, $mapping);

        $repository =  $this->get('open_orchestra_model.repository.content');

        $collection = $repository->findForPaginateFilterByContentTypeSiteAndLanguage($configuration, $contentTypeId, $siteId, $language, $searchTypes);
        $recordsTotal = $repository->countFilterByContentTypeSiteAndLanguage($contentTypeId, $siteId, $language);
        $recordsFiltered = $repository->countWithFilterAndContentTypeSiteAndLanguage($configuration, $contentTypeId, $siteId, $language, $searchTypes);
        $facade = $this->get('open_orchestra_api.transformer_manager')->get('content_collection')->transform($collection);
        $facade->recordsTotal = $recordsTotal;
        $facade->recordsFiltered = $recordsFiltered;

        return $facade;
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/duplicate", name="open_orchestra_api_content_duplicate")
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function duplicateAction(Request $request)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, ContentInterface::ENTITY_TYPE);

        $format = $request->get('_format', 'json');
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            $this->getParameter('open_orchestra_api.facade.content.class'),
            $format
        );
        $content = $this->get('open_orchestra_api.transformer_manager')->get('content')->reverseTransform($facade);
        $frontLanguages = $this->getParameter('open_orchestra_backoffice.orchestra_choice.front_language');

        $contentId = $content->getContentId();
        $newContentId = null;
        foreach (array_keys($frontLanguages) as $language) {
            $content = $this->findOneContent($contentId, $language);
            if ($content instanceof ContentInterface) {
                $duplicateContent = $this->get('open_orchestra_backoffice.manager.content')->duplicateContent($content, $newContentId);
                $objectManager = $this->get('object_manager');
                $objectManager->persist($duplicateContent);
                $objectManager->flush();

                $newContentId = $duplicateContent->getContentId();
                $this->dispatchEvent(ContentEvents::CONTENT_DUPLICATE, new ContentEvent($duplicateContent));
            }
        }

        return array();
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/delete-multiple", name="open_orchestra_api_content_delete_multiple")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteContentsAction(Request $request)
    {
        $format = $request->get('_format', 'json');
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            $this->getParameter('open_orchestra_api.facade.content_collection.class'),
            $format
        );
        $contents = $this->get('open_orchestra_api.transformer_manager')->get('content_collection')->reverseTransform($facade);
        $contentIds = array();
        foreach ($contents as $content) {
            if ($this->isGranted(ContributionActionInterface::DELETE, $content) && !$this->isContentIdUsed($content->getContentId())) {
                $contentIds[] = $content->getContentId();
                $this->dispatchEvent(ContentEvents::CONTENT_DELETE, new ContentEvent($content));
            }
        }

        $this->get('open_orchestra_model.repository.content')->removeContentIds($contentIds);

        return array();
    }

    /**
     * @param Request $request
     * @param string  $contentId
     * @param string  $language
     *
     * @Config\Route("/delete-multiple-version/{contentId}/{language}", name="open_orchestra_api_content_delete_multiple_versions")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteContentVersionsAction(Request $request, $contentId, $language)
    {
        $format = $request->get('_format', 'json');
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            $this->getParameter('open_orchestra_api.facade.content_collection.class'),
            $format
        );
        $contents = $this->get('open_orchestra_api.transformer_manager')->get('content_collection')->reverseTransform($facade);
        $versionsCount = $this->get('open_orchestra_model.repository.content')->countNotDeletedByLanguage($contentId, $language);
        if ($versionsCount > count($contents)) {
            $storageIds = array();
            foreach ($contents as $content) {
                if ($this->isGranted(ContributionActionInterface::DELETE, $content) && !$content->getStatus()->isPublishedState()) {
                    $storageIds[] = $content->getId();
                    $this->dispatchEvent(ContentEvents::CONTENT_DELETE, new ContentEvent($content));
                }
            }
            $this->get('open_orchestra_model.repository.content')->removeContentVersion($storageIds);
        }

        return array();
    }

    /**
     * @param string $contentId
     *
     * @Config\Route("/{contentId}/delete", name="open_orchestra_api_content_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($contentId)
    {
        $repository = $this->get('open_orchestra_model.repository.content');
        $content = $repository->findOneByContentId($contentId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::DELETE, $content);

        if (!$this->isContentIdUsed($contentId)) {
            $repository->removeContentIds(array($contentId));
        }

        return array();
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

    /**
     * @param Request $request
     * @param string  $contentId
     * @param string  $language
     * @param string  $originalVersion
     *
     * @Config\Route("/new-version/{contentId}/{language}/{originalVersion}", name="open_orchestra_api_content_new_version")
     * @Config\Method({"POST"})
     *
     * @return Response
     * @throws ContentNotFoundHttpException
     */
    public function newVersionAction(Request $request, $contentId, $language, $originalVersion)
    {
        /** @var ContentInterface $content */
        $content = $this->findOneContent($contentId, $language, $originalVersion);
        if (!$content instanceof ContentInterface) {
            throw new ContentNotFoundHttpException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $content);

        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'OpenOrchestra\ApiBundle\Facade\ContentFacade',
            $request->get('_format', 'json')
        );
        $newContent = $this->get('open_orchestra_backoffice.manager.content')->newVersionContent($content, $facade->versionName);

        $objectManager = $this->get('object_manager');
        $objectManager->persist($newContent);
        $objectManager->flush();
        $this->dispatchEvent(ContentEvents::CONTENT_DUPLICATE, new ContentEvent($newContent));

        return array();
    }

    /**
     * @param string  $contentId
     * @param string  $language
     *
     * @Config\Route("/list-version/{contentId}/{language}", name="open_orchestra_api_content_list_version")
     * @Config\Method({"GET"})
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AUTHORIZATIONS_DELETE_VERSION
     * })
     * @return Response
     */
    public function listVersionAction($contentId, $language)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, SiteInterface::ENTITY_TYPE);
        $contents = $this->get('open_orchestra_model.repository.content')->findNotDeletedSortByUpdatedAt($contentId, $language);

        return $this->get('open_orchestra_api.transformer_manager')->get('content_collection')->transform($contents);
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
     * @param string   $contentId
     *
     * @return boolean
     */
    protected function isContentIdUsed($contentId)
    {
        $publishedContents = $this->get('open_orchestra_model.repository.content')->findAllPublishedByContentId($contentId);
        $isUsed = false;
        foreach ($publishedContents as $publishedContent) {
            $isUsed = $isUsed || $publishedContent->isUsed();
        }

        return $isUsed;
    }
}
