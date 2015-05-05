<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\BaseApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseApiBundle\Context\GroupContext;
use OpenOrchestra\BaseApiBundle\Controller\BaseController;

/**
 * Class ContentController
 *
 * @Config\Route("content")
 */
class ContentController extends BaseController
{
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
     */
    public function showAction(Request $request, $contentId)
    {
        $language = $request->get('language');
        $version = $request->get('version');
        $contentRepository = $this->get('open_orchestra_model.repository.content');
        $content = $contentRepository->findOneByContentIdAndLanguageAndVersion($contentId, $language, $version);

        if (!$content) {
            $oldContent = $contentRepository->findOneByContentIdAndLanguageAndVersion($contentId);
            $content = $this->get('open_orchestra_backoffice.manager.content')->createNewLanguageContent($oldContent, $language);
            $dm = $this->get('doctrine.odm.mongodb.document_manager');
            $dm->persist($content);
            $dm->flush($content);
        }

        return $this->get('open_orchestra_api.transformer_manager')->get('content')->transform($content);
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

        $contentCollection = $this->get('open_orchestra_model.repository.content')->findByContentTypeInLastVersion($contentType);

        return $this->get('open_orchestra_api.transformer_manager')->get('content_collection')->transform($contentCollection, $contentType);
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
        $this->get('doctrine.odm.mongodb.document_manager')->flush();
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
        $language = $request->get('language');
        /** @var ContentInterface $content */
        $content = $this->get('open_orchestra_model.repository.content')->findOneByContentIdAndLanguageAndVersion($contentId, $language);
        $newContent = $this->get('open_orchestra_backoffice.manager.content')->duplicateContent($content);


        $em = $this->get('doctrine.odm.mongodb.document_manager');
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
        $language = $request->get('language');
        $contents = $this->get('open_orchestra_model.repository.content')->findByContentIdAndLanguage($contentId, $language);

        return $this->get('open_orchestra_api.transformer_manager')->get('content_collection')->transform($contents);
    }

    /**
     * @param Request $request
     * @param string $contentMongoId
     *
     * @Config\Route("/update/{contentMongoId}", name="open_orchestra_api_content_update")
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
}
