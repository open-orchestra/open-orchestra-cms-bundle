<?php

namespace PHPOrchestra\ApiBundle\Controller;

use PHPOrchestra\ApiBundle\Exceptions\FolderNotDeletableException;
use PHPOrchestra\ApiBundle\Facade\FacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PHPOrchestra\ApiBundle\Controller\Annotation as Api;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FolderController
 *
 * @Config\Route("folder")
 */
class FolderController extends Controller
{
    /**
     * @param string $folderId
     *
     * @Config\Route("/{folderId}/delete", name="php_orchestra_api_folder_delete")
     * @Config\Method({"DELETE"})
     *
     * @throws FolderNotDeletableException
     *
     * @return Response
     */
    public function deleteAction($folderId)
    {
        $folder = $this->get('php_orchestra_model.repository.media_folder')->find($folderId);

        if ($folder) {
            $folderManager = $this->get('php_orchestra_backoffice.manager.media_folder');

            if ($folderManager->isDeletable($folder)) {
                $folderManager->deleteTree($folder);
                $this->get('doctrine.odm.mongodb.document_manager')->flush();
            } else {
                throw new FolderNotDeletableException('php_orchestra_backoffice.form.folder.delete');
            }
        }

        return new Response('', 200);
    }
}
