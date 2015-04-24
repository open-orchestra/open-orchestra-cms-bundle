<?php

namespace OpenOrchestra\MediaAdminBundle\Controller\Api;

use OpenOrchestra\ApiBundle\Controller\BaseController;
use OpenOrchestra\Media\Event\FolderEvent;
use OpenOrchestra\Media\FolderEvents;
use OpenOrchestra\MediaAdminBundle\Exceptions\HttpException\FolderNotDeletableException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FolderController
 *
 * @Config\Route("folder")
 */
class FolderController extends BaseController
{
    /**
     * @param string $folderId
     *
     * @Config\Route("/{folderId}/delete", name="open_orchestra_api_folder_delete")
     * @Config\Method({"DELETE"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_TREE_FOLDER')")
     *
     * @throws FolderNotDeletableException
     *
     * @return Response
     */
    public function deleteAction($folderId)
    {
        $folder = $this->get('open_orchestra_media.repository.media_folder')->find($folderId);

        if ($folder) {
            $folderManager = $this->get('open_orchestra_media_admin.manager.media_folder');

            if (!$folderManager->isDeletable($folder)) {
                throw new FolderNotDeletableException();
            }
            $folderManager->deleteTree($folder);
            $this->dispatchEvent(FolderEvents::FOLDER_DELETE, new FolderEvent($folder));
            $this->get('doctrine.odm.mongodb.document_manager')->flush();
        }

        return new Response('', 200);
    }
}
