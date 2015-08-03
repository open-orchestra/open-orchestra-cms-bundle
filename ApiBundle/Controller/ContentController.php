<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Controller\ControllerTrait\HandleRequestDataTable;
use OpenOrchestra\ApiBundle\Controller\ControllerTrait\ListStatus;
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

/**
 * Class ContentController
 *
 * @Config\Route("content")
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
     * @Config\Security("has_role('ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT')")
     *
     * @Api\Serialize()
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
     * @Config\Security("has_role('ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT')")
     *
     * @Api\Serialize()
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
     * @Config\Route("", name="open_orchestra_api_content_list")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT')")
     *
     * @Api\Serialize()
     *
     * @Api\Groups({GroupContext::G_HIDE_ROLES})
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $contentType = $request->get('content_type');
        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();

        $repository =  $this->get('open_orchestra_model.repository.content');
        $transformer = $this->get('open_orchestra_api.transformer_manager')->get('content_collection');

        if ($request->get('entityId') && $request->get('language')) {
            $content = $this->showOrCreate($request, $request->get('entityId'));
            return $transformer->transform(array($content), $contentType);
        }

        $configuration = PaginateFinderConfiguration::generateFromRequest($request);
        $configuration->setDescriptionEntity(array(
            'name'         => array('key' => 'name'),
            'status_label' => array('key' => 'status.name'),
            'version'      => array('key' => 'version' , 'type' => 'integer'),
            'language'     => array('key' => 'language'),
        ));
        $contentCollection = $repository->findByContentTypeAndSiteIdInLastVersionForPaginate($contentType, $configuration, $siteId);
        $recordsTotal = $repository->countByContentTypeInLastVersion($contentType);
        $recordsFiltered = $repository->countByContentTypeInLastVersionWithFilter($contentType, $configuration);

        $facade = $transformer->transform($contentCollection, $contentType);
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
     * @Config\Security("has_role('ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT')")
     *
     * @return Response
     */
    public function deleteAction($contentId)
    {
        $content = $this->get('open_orchestra_model.repository.content')->find($contentId);
        $content->setDeleted(true);
        $this->get('object_manager')->flush();
        $this->dispatchEvent(ContentEvents::CONTENT_DELETE, new ContentEvent($content));

        return new Response('', 200);
    }

    /**
     * @param Request $request
     * @param string  $contentId
     *
     * @Config\Route("/{contentId}/duplicate", name="open_orchestra_api_content_duplicate")
     * @Config\Method({"POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT')")
     *
     * @return Response
     */
    public function duplicateAction(Request $request, $contentId)
    {
        /** @var ContentInterface $content */
        $content = $this->findOneContent($contentId, $request->get('language'));
        $newContent = $this->get('open_orchestra_backoffice.manager.content')->duplicateContent($content);


        $em = $this->get('object_manager');
        $em->persist($newContent);
        $em->flush();

        $this->dispatchEvent(ContentEvents::CONTENT_DUPLICATE, new ContentEvent($newContent));

        return new Response('', 200);
    }

    /**
     * @param Request $request
     * @param string  $contentId
     *
     * @Config\Route("/{contentId}/list-version", name="open_orchestra_api_content_list_version")
     * @Config\Method({"GET"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT')")
     *
     * @Api\Serialize()
     *
     * @return Response
     */
    public function listVersionAction(Request $request, $contentId)
    {
        $contents = $this->get('open_orchestra_model.repository.content')->findByContentIdAndLanguage($contentId, $request->get('language'));

        return $this->get('open_orchestra_api.transformer_manager')->get('content_collection')->transform($contents);
    }

    /**
     * @param Request $request
     * @param string $contentMongoId
     *
     * @Config\Route("/{contentMongoId}/update", name="open_orchestra_api_content_update")
     * @Config\Method({"POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT')")
     *
     * @Api\Serialize()
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
     * @Config\Security("has_role('ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT')")
     *
     * @Api\Serialize()
     *
     * @return Response
     */
    public function listStatusesForContentAction($contentMongoId)
    {
        $content = $this->get('open_orchestra_model.repository.content')->find($contentMongoId);

        return $this->listStatuses($content->getStatus());
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
        $content = $contentRepository->findOneByContentIdAndLanguageAndVersion($contentId, $language, $version);

        return $content;
    }
}
