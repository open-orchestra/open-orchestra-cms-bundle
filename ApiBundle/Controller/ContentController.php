<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\ListStatus;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\ContentNotDeletableException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\ContentNotFoundHttpException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\ContentTypeNotAllowedException;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\StatusChangeNotGrantedHttpException;
use OpenOrchestra\Backoffice\BusinessRules\Strategies\BusinessActionInterface;
use OpenOrchestra\Backoffice\BusinessRules\Strategies\ContentStrategy;
use OpenOrchestra\Backoffice\BusinessRules\Strategies\ContentTypeStrategy;
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
     * @throws ContentTypeNotAllowedException
     * @throws ContentNotFoundHttpException
     */
    public function showAction($contentId, $language, $version)
    {
        if (null === $language) {
            $language = $this->get('open_orchestra_backoffice.context_backoffice_manager')->getSiteDefaultLanguage();
        }
        $content = $this->findOneContent($contentId, $language, $version);

        if (!$content) {
            throw new ContentNotFoundHttpException();
        }

        if (!$this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(BusinessActionInterface::READ, $content)) {
            throw new ContentTypeNotAllowedException();
        }

        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, $content);

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
        $this->denyAccessUnlessGranted(ContributionActionInterface::READ, ContentInterface::ENTITY_TYPE);

        $contentType = $this->get('open_orchestra_model.repository.content_type')->findOneByContentTypeIdInLastVersion($contentTypeId);
        if (!$this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(ContentTypeStrategy::READ_LIST, $contentType)) {
            throw new ContentTypeNotAllowedException();
        }

        $mapping = $this->getMappingContentType($language, $contentType);

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
        $format = $request->get('_format', 'json');
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            $this->getParameter('open_orchestra_api.facade.content.class'),
            $format
        );
        $content = $this->get('open_orchestra_api.transformer_manager')->get('content')->reverseTransform($facade);
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, $content);

        if (!$this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(BusinessActionInterface::READ, $content)) {
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
     *
     * @Config\Route("/delete-multiple-version", name="open_orchestra_api_content_delete_multiple_versions")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteContentVersionsAction(Request $request)
    {
        $format = $request->get('_format', 'json');
        $facade = $this->get('jms_serializer')->deserialize(
            $request->getContent(),
            $this->getParameter('open_orchestra_api.facade.content_collection.class'),
            $format
        );
        $contents = $this->get('open_orchestra_api.transformer_manager')->get('content_collection')->reverseTransform($facade);
        $storageIds = array();
        foreach ($contents as $content) {
            if ($this->isGranted(ContributionActionInterface::DELETE, $content) &&
                $this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(ContentStrategy::DELETE_VERSION, $content)
            ) {
                $storageIds[] = $content->getId();
                $this->dispatchEvent(ContentEvents::CONTENT_DELETE_VERSION, new ContentEvent($content));
            }
        }
        if (!empty($storageIds)) {
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
                $this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(BusinessActionInterface::DELETE, $content) &&
                $this->isGranted(ContributionActionInterface::DELETE, $content)
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
     * @throws ContentTypeNotAllowedException,
     * @throws ContentNotDeletableException
     */
    public function deleteAction($contentId)
    {
        $repository = $this->get('open_orchestra_model.repository.content');
        $content = $repository->findOneByContentId($contentId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::DELETE, $content);

        if (!$this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(BusinessActionInterface::DELETE, $content)) {
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
     * @Api\Groups({
     *     OpenOrchestra\ApiBundle\Context\CMSGroupContext::AUTHORIZATIONS
     * })
     *
     * @return FacadeInterface
     */
    public function listContentByAuthorAndSiteIdAction($published)
    {
        $siteId = $this->get('open_orchestra_backoffice.context_backoffice_manager')->getSiteId();
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
     * @throws ContentTypeNotAllowedException
     * @throws ContentNotFoundHttpException
     */
    public function newVersionAction(Request $request, $contentId, $language, $originalVersion)
    {
        /** @var ContentInterface $content */
        $content = $this->findOneContent($contentId, $language, $originalVersion);

        if (!$content instanceof ContentInterface) {
            throw new ContentNotFoundHttpException();
        }

        if (!$this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(BusinessActionInterface::READ, $content)) {
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
     * @throws ContentTypeNotAllowedException
     * @throws ContentNotFoundHttpException
     */
    public function newLanguageAction($contentId, $language)
    {
        $content = $this->get('open_orchestra_model.repository.content')->findLastVersion($contentId);

        if (!$content instanceof ContentInterface) {
            throw new ContentNotFoundHttpException();
        }

        if (!$this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(BusinessActionInterface::READ, $content)) {
            throw new ContentTypeNotAllowedException();
        }

        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $content);

        $newContent = $this->get('open_orchestra_backoffice.manager.content')->createNewLanguageContent($content, $language);

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
     * @Config\Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     * @throws ContentTypeNotAllowedException
     */
    public function listVersionAction($contentId, $language)
    {
        $contents = $this->get('open_orchestra_model.repository.content')->findNotDeletedSortByUpdatedAt($contentId, $language);

        foreach ($contents as $content) {
            if (!$this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(BusinessActionInterface::READ, $content)) {
                throw new ContentTypeNotAllowedException();
            }
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
     * @throws ContentTypeNotAllowedException
     * @throws ContentNotFoundHttpException
     */
    public function listStatusesForContentAction($contentId, $language, $version)
    {
        $content = $this->findOneContent($contentId, $language, $version);

        if (!$content instanceof ContentInterface) {
            throw new ContentNotFoundHttpException();
        }

        if (!$this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(BusinessActionInterface::READ, $content)) {
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
     * @throws ContentTypeNotAllowedException
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

        if (!$this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(BusinessActionInterface::READ, $content)) {
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
}
