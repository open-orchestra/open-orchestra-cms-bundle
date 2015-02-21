<?php

namespace OpenOrchestra\ApiBundle\Controller;

use OpenOrchestra\ApiBundle\Exceptions\HttpException\MediaNotDeletableException;
use OpenOrchestra\ApiBundle\Facade\FacadeInterface;
use OpenOrchestra\Media\Event\MediaEvent;
use OpenOrchestra\Media\MediaEvents;
use OpenOrchestra\Media\Model\FolderInterface;
use OpenOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MediaController
 *
 * @Config\Route("media")
 */
class MediaController extends BaseController
{
    /**
     * @param int $mediaId
     *
     * @Config\Route("/{mediaId}", name="open_orchestra_api_media_show")
     * @Config\Method({"GET"})
     *
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function showAction($mediaId)
    {
        $media = $this->get('open_orchestra_media.repository.media')->find($mediaId);

        return $this->get('open_orchestra_api.transformer_manager')->get('media')->transform($media);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("", name="open_orchestra_api_media_list")
     * @Config\Method({"GET"})
     * @Api\Serialize()
     *
     * @return FacadeInterface
     */
    public function listAction(Request $request)
    {
        $folderId = $request->get('folderId');
        /** @var FolderInterface $folder */
        $folder = $this->get('open_orchestra_media.repository.media_folder')->find($folderId);
        $folderDeletable = $this->get('open_orchestra_backoffice.manager.media_folder')->isDeletable($folder);
        $parentId = null;
        if ($folder->getParent() instanceof FolderInterface) {
            $parentId = $folder->getParent()->getId();
        }
        $mediaCollection = $this->get('open_orchestra_media.repository.media')->findByFolderId($folderId);

        return $this->get('open_orchestra_api.transformer_manager')->get('media_collection')->transform(
            $mediaCollection,
            $folderId,
            $folderDeletable,
            $parentId
        );
    }

    /**
     * @param $mediaId
     *
     * @Config\Route("/{mediaId}/delete", name="open_orchestra_api_media_delete")
     * @Config\Method({"DELETE"})
     *
     * @return Response
     *
     * @throws MediaNotDeletableException
     */
    public function deleteAction($mediaId)
    {
        $media = $this->get('open_orchestra_media.repository.media')->find($mediaId);
        if (!$media->isDeletable()) {
            throw new MediaNotDeletableException();
        }

        $this->dispatchEvent(MediaEvents::MEDIA_DELETE, new MediaEvent($media));
        $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
        $documentManager->remove($media);
        $documentManager->flush();

        return new Response('', 200);
    }
}
