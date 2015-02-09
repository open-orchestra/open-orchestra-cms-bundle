<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use PHPOrchestra\ModelInterface\ContentEvents;
use PHPOrchestra\ModelInterface\Event\ContentEvent;
use PHPOrchestra\ModelInterface\Event\NodeEvent;
use PHPOrchestra\ModelInterface\Model\ContentInterface;
use PHPOrchestra\ModelInterface\NodeEvents;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @Config\Route("/{contentId}", name="php_orchestra_api_content_show")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction(Request $request, $contentId)
    {
        $language = $request->get('language');
        $version = $request->get('version');
        $contentRepository = $this->get('php_orchestra_model.repository.content');
        $content = $contentRepository->findOneByContentIdAndLanguageAndVersion($contentId, $language, $version);

        if (!$content) {
            $oldContent = $contentRepository->findOneByContentIdAndLanguageAndVersion($contentId);
            $content = $this->get('php_orchestra_backoffice.manager.content')->createNewLanguageContent($oldContent, $language);
            $dm = $this->get('doctrine.odm.mongodb.document_manager');
            $dm->persist($content);
            $dm->flush($content);
        }

        return $this->get('php_orchestra_api.transformer_manager')->get('content')->transform($content);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("", name="php_orchestra_api_content_list")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $contentType = $request->get('content_type');

        $contentCollection = $this->get('php_orchestra_model.repository.content')->findByContentTypeInLastVersion($contentType);

        return $this->get('php_orchestra_api.transformer_manager')->get('content_collection')->transform($contentCollection, $contentType);
    }

    /**
     * @param string $contentId
     *
     * @Config\Route("/{contentId}/delete", name="php_orchestra_api_content_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     */
    public function deleteAction($contentId)
    {
        $content = $this->get('php_orchestra_model.repository.content')->find($contentId);
        $content->setDeleted(true);
        $this->get('doctrine.odm.mongodb.document_manager')->flush();
        $this->dispatchEvent(ContentEvents::CONTENT_DELETE, new ContentEvent($content));

        return new Response('', 200);
    }

    /**
     * @param Request $request
     * @param string  $contentId
     *
     * @Config\Route("/{contentId}/duplicate", name="php_orchestra_api_content_duplicate")
     * @Config\Method({"POST"})
     *
     * @return Response
     */
    public function duplicateAction(Request $request, $contentId)
    {
        $language = $request->get('language');
        /** @var ContentInterface $content */
        $content = $this->get('php_orchestra_model.repository.content')->findOneByContentIdAndLanguageAndVersion($contentId, $language);
        $newContent = $this->get('php_orchestra_backoffice.manager.content')->duplicateContent($content);


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
     * @Config\Route("/{contentId}/list-version", name="php_orchestra_api_content_list_version")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return Response
     */
    public function listVersionAction(Request $request, $contentId)
    {
        $language = $request->get('language');
        $contents = $this->get('php_orchestra_model.repository.content')->findByContentIdAndLanguage($contentId, $language);

        return $this->get('php_orchestra_api.transformer_manager')->get('content_collection')->transform($contents);
    }

    /**
     * @param Request $request
     * @param string $contentMongoId
     *
     * @Config\Route("/update/{contentMongoId}", name="php_orchestra_api_content_update")
     * @Config\Method({"POST"})
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
            'PHPOrchestra\ModelInterface\Event\ContentEvent'
        );
    }
}
