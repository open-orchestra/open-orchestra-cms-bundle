<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\ListStatus;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\ContentNotDeletableException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\ContentNotFoundHttpException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\ContentTypeNotAllowedException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\StatusChangeNotGrantedHttpException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentDeleteEvent;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
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
     * @param string  $contentId
     * @param string  $version
     * @param string  $language
     *
     * @Config\Route(
     *     "/show/{contentId}/{language}/{version}",
     *     name="open_orchestra_api_content_show",
     *     defaults={"version": null, "language": null},
     * )
     * @Config\Method({"GET"})
     *
     * @return FacadeInterface
     * @throws ContentNotFoundHttpException, ContentTypeNotAllowedException
     */
    public function showAction($contentId, $language, $version)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, SiteInterface::ENTITY_TYPE);
        if (null === $language) {
            $language = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteDefaultLanguage();
        }
        $content = $this->findOneContent($contentId, $language, $version);

        if (!$content) {
            throw new ContentNotFoundHttpException();
        }
        if (!$this->isContentOnSiteAllowed($content)) {
            throw new ContentTypeNotAllowedException();
        }

        return $this->get('open_orchestra_api.transformer_manager')->get('content')->transform($content);
    }

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
     * @throws ContentTypeNotAllowedException
     */
    public function listAction(Request $request, $contentTypeId, $siteId, $language)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, SiteInterface::ENTITY_TYPE);

        $contentType = $this->get('open_orchestra_model.repository.content_type')->findOneByContentTypeIdInLastVersion($contentTypeId);
        $mapping = $this->getMappingContentType($language, $contentType);

        if (!$this->isContentTypeOnSiteAllowed($contentType)) {
            throw new ContentTypeNotAllowedException();
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
     * @throws ContentTypeNotAllowedException
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

        if (!$this->isContentOnSiteAllowed($content)) {
            throw new ContentTypeNotAllowedException();
        }

        $frontLanguages = $this->getParameter('open_orchestra_backoffice.orchestra_choice.front_language');

        $contentId = $content->getContentId();
        $newContentId = null;
        $objectManager = $this->get('object_manager');

        foreach (array_keys($frontLanguages) as $language) {
            $content = $this->findOneContent($contentId, $language);
            if ($content instanceof ContentInterface) {
                $duplicateContent = $this->get('open_orchestra_backoffice.manager.content')->duplicateContent($content, $newContentId);
                $objectManager->persist($duplicateContent);

                $newContentId = $duplicateContent->getContentId();
                $this->dispatchEvent(ContentEvents::CONTENT_DUPLICATE, new ContentEvent($duplicateContent));
            }
        }
        $objectManager->flush();

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
                if ($this->isGranted(ContributionActionInterface::DELETE, $content)
                    && !$content->getStatus()->isPublishedState()
                    && $this->isContentOnSiteAllowed($content)
                ) {
                    $storageIds[] = $content->getId();
                    $this->dispatchEvent(ContentEvents::CONTENT_DELETE_VERSION, new ContentEvent($content));
                }
            }
            $this->get('open_orchestra_model.repository.content')->removeContentVersion($storageIds);
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
        $repository = $this->get('open_orchestra_model.repository.content');

        foreach ($contents as $content) {
            $this->denyAccessUnlessGranted(ContributionActionInterface::DELETE, $content);
            $contentId = $content->getContentId();
            if (
                false === $repository->hasContentIdWithoutAutoUnpublishToState($contentId) &&
                $this->isGranted(ContributionActionInterface::DELETE, $content)
                && $this->isContentOnSiteAllowed($content)
            ) {
                $repository->softDeleteContent($contentId);
                $this->dispatchEvent(ContentEvents::CONTENT_DELETE, new ContentDeleteEvent($contentId, $content->getSiteId()));
            }
        }

        return array();
    }

    /**
     * @param string $contentId
     *
     * @Config\Route("/delete/{contentId}", name="open_orchestra_api_content_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     * @throws ContentTypeNotAllowedException, ContentNotDeletableException
     */
    public function deleteAction($contentId)
    {
        $repository = $this->get('open_orchestra_model.repository.content');
        $content = $repository->findOneByContentId($contentId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::DELETE, $content);

        if (!$this->isContentOnSiteAllowed($content)) {
            throw new ContentTypeNotAllowedException();
        }

        if (true === $repository->hasContentIdWithoutAutoUnpublishToState($contentId)) {
            throw new ContentNotDeletableException();
        }

        $repository->softDeleteContent($contentId);
        $this->dispatchEvent(ContentEvents::CONTENT_DELETE, new ContentDeleteEvent($contentId, $content->getSiteId()));

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
        $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);
        $availableContentTypes = $site->getContentTypes();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $contents = $this->get('open_orchestra_model.repository.content')->findByHistoryAndSiteId(
            $user->getId(),
            $siteId,
            array(ContentEvents::CONTENT_CREATION, ContentEvents::CONTENT_UPDATE),
            $published,
            10,
            array('histories.updatedAt' => -1),
            $availableContentTypes
        );

        return $this->get('open_orchestra_api.transformer_manager')->get('content_collection')->transform($contents);
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
     * @throws ContentNotFoundHttpException, ContentTypeNotAllowedException
     */
    public function newVersionAction(Request $request, $contentId, $language, $originalVersion)
    {
        /** @var ContentInterface $content */
        $content = $this->findOneContent($contentId, $language, $originalVersion);

        if (!$content instanceof ContentInterface) {
            throw new ContentNotFoundHttpException();
        }

        if (!$this->isContentOnSiteAllowed($content)) {
            throw new ContentTypeNotAllowedException();
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
     * @Config\Route("/new-language/{contentId}/{language}", name="open_orchestra_api_content_new_language")
     * @Config\Method({"POST"})
     *
     * @return Response
     * @throws ContentNotFoundHttpException, ContentTypeNotAllowedException
     */
    public function newLanguageAction($contentId, $language)
    {
        $content = $this->get('open_orchestra_model.repository.content')->findLastVersion($contentId);

        if (!$content instanceof ContentInterface) {
            throw new ContentNotFoundHttpException();
        }

        if (!$this->isContentOnSiteAllowed($content)) {
            throw new ContentTypeNotAllowedException();
        }

        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $content);

        $newContent = $this->get('open_orchestra_backoffice.manager.content')->newVersionContent($content);
        $status = $this->get('open_orchestra_model.repository.status')->findOneByTranslationState();
        $newContent->setStatus($status);
        $newContent->setLanguage($language);
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
     * @throws ContentTypeNotAllowedException
     */
    public function listVersionAction($contentId, $language)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, SiteInterface::ENTITY_TYPE);
        $contents = $this->get('open_orchestra_model.repository.content')->findNotDeletedSortByUpdatedAt($contentId, $language);

        if (!$this->isContentsOnSiteAllowed($contents)) {
            throw new ContentTypeNotAllowedException();
        }

        return $this->get('open_orchestra_api.transformer_manager')->get('content_collection')->transform($contents);
    }

    /**
     * @param string $contentId
     * @param string $language
     * @param string $version
     *
     * @Config\Route(
     *     "/list-statuses/{contentId}/{language}/{version}",
     *     name="open_orchestra_api_content_list_status")
     * @Config\Method({"GET"})
     *
     * @return Response
     * @throws ContentNotFoundHttpException, ContentTypeNotAllowedException
     */
    public function listStatusesForContentAction($contentId, $language, $version)
    {
        $content = $this->findOneContent($contentId, $language, $version);

        if (!$content instanceof ContentInterface) {
            throw new ContentNotFoundHttpException();
        }

        if ($this->isContentOnSiteAllowed($content)) {
            throw new ContentTypeNotAllowedException();
        }

        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, $content);

        return $this->listStatuses($content);
    }

    /**
     * @param Request $request
     * @param boolean $saveOldPublishedVersion
     *
     * @Config\Route(
     *     "/update-status",
     *     name="open_orchestra_api_content_update_status",
     *     defaults={"saveOldPublishedVersion": false},
     * )
     * @Config\Route(
     *     "/update-status-with-save-published-version",
     *     name="open_orchestra_api_content_update_status_with_save_published",
     *     defaults={"saveOldPublishedVersion": true},
     * )
     * @Config\Method({"PUT"})
     *
     * @return Response
     * @throws ContentNotFoundHttpException
     * @throws StatusChangeNotGrantedHttpException
     */
    public function changeStatusAction(Request $request, $saveOldPublishedVersion)
    {
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            'OpenOrchestra\ApiBundle\Facade\ContentFacade',
            $request->get('_format', 'json')
        );

        $content = $this->get('open_orchestra_model.repository.content')->find($facade->id);
        if (!$content instanceof ContentInterface) {
            throw new ContentNotFoundHttpException();
        }

        if ($this->isContentOnSiteAllowed($content)) {
            throw new ContentTypeNotAllowedException();
        }

        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $content);
        $contentSource = clone $content;

        $this->get('open_orchestra_api.transformer_manager')->get('content')->reverseTransform($facade, $content);
        $status = $content->getStatus();
        if ($status !== $contentSource->getStatus()) {
            if (!$this->isGranted($status, $contentSource)) {
                throw new StatusChangeNotGrantedHttpException();
            }

            $this->updateStatus($contentSource, $content, $saveOldPublishedVersion);
        }

        return array();
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
     * @param ContentInterface $contentSource
     * @param ContentInterface $content
     * @param boolean          $saveOldPublishedVersion
     */
    protected function updateStatus(
        ContentInterface $contentSource,
        ContentInterface $content,
        $saveOldPublishedVersion
    ) {
        if (true === $content->getStatus()->isPublishedState() && false === $saveOldPublishedVersion) {
            $oldPublishedVersion = $this->get('open_orchestra_model.repository.content')->findOnePublished(
                $content->getContentId(),
                $content->getLanguage(),
                $content->getSiteId()
            );
            if ($oldPublishedVersion instanceof ContentInterface) {
                $this->get('object_manager')->remove($oldPublishedVersion);
            }
        }

        $this->get('object_manager')->flush();
        $event = new ContentEvent($content, $contentSource->getStatus());
        $this->dispatchEvent(ContentEvents::CONTENT_CHANGE_STATUS, $event);
    }

    /**
     * @param string               $language
     * @param ContentTypeInterface $contentType
     *
     * @return array
     */
    protected function getMappingContentType($language, ContentTypeInterface $contentType)
    {
        $mapping = array(
            'name' => 'name',
            'status_label' => 'status.labels.' . $language,
            'linked_to_site' => 'linkedToSite',
            'created_at' => 'createdAt',
            'created_by' => 'createdBy',
            'updated_at' => 'updatedAt',
            'updated_by' => 'updatedBy',
        );
        foreach ($contentType->getDefaultListable() as $column => $isListable) {
            if (!$isListable) {
                unset($mapping[$column]);
            }
        }
        foreach ($contentType->getFields() as $field) {
            $mapping['fields.' . $field->getFieldId() . '.string_value'] = 'attributes.' . $field->getFieldId() . '.stringValue';
        }

        return $mapping;
    }

    /**
     * @param ContentTypeInterface $contentType
     *
     * @return bool
     */
    protected function isContentTypeOnSiteAllowed(ContentTypeInterface $contentType)
    {
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);
        $availableContentTypes = $site->getContentTypes();

        return in_array($contentType->getContentTypeId(), $availableContentTypes);
    }

    /**
     * @param ContentInterface $content
     * @param array            $availableContentTypes
     *
     * @return bool
     */
    protected function isContentOnSiteAllowed(ContentInterface $content, $availableContentTypes = array())
    {
        if (empty($availableContentTypes)) {
            $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
            $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);
            $availableContentTypes = $site->getContentTypes();
        }

        return in_array($content->getContentType(), $availableContentTypes);
    }

    /**
     * @param array $contents
     *
     * @return bool
     */
    protected function isContentsOnSiteAllowed(array $contents)
    {
        $result = true;
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);
        $availableContentTypes = $site->getContentTypes();

        foreach ($contents as $content) {
            $result = $result && $this->isContentOnSiteAllowed($content, $availableContentTypes);
        }

        return $result;
    }
}
