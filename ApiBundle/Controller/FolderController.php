<?php

namespace PHPOrchestra\ApiBundle\Controller;

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
     * @return Response
     */
    public function deleteAction($folderId)
    {
        $this->get('php_orchestra_backoffice.manager.media_folder')->deleteTree($folderId);
        $this->get('doctrine.odm.mongodb.document_manager')->flush();

        return new Response('', 200);
    }
}
