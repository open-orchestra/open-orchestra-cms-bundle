<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\Media\Event\FolderEvent;
use PHPOrchestra\Media\FolderEvents;
use PHPOrchestra\Media\Model\FolderInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FolderController
 */
class FolderController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $folderId
     *
     * @Config\Route("/folder/form/{folderId}", name="php_orchestra_backoffice_folder_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $folderId)
    {
        $folderRepository = $this->container->get('php_orchestra_media.repository.media_folder');
        $folder = $folderRepository->find($folderId);

        $url = $this->generateUrl('php_orchestra_backoffice_folder_form', array('folderId' => $folderId));

        return $this->generateForm($request, $folder, $url, FolderEvents::FOLDER_UPDATE);
    }

    /**
     * @param Request $request
     * @param string  $parentId
     *
     * @Config\Route("/folder/new/{parentId}", name="php_orchestra_backoffice_folder_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request, $parentId)
    {
        $parentFolder = $this->container->get('php_orchestra_media.repository.media_folder')->find($parentId);
        $folderClass = $this->container->getParameter('php_orchestra_media.document.media_folder.class');
        $folder = new $folderClass();
        if ($parentFolder) {
            $folder->setParent($parentFolder);
        }

        $url = $this->generateUrl('php_orchestra_backoffice_folder_new', array('parentId' => $parentId));

        return $this->generateForm($request, $folder, $url, FolderEvents::FOLDER_CREATE);
    }

    /**
     * @param Request         $request
     * @param FolderInterface $folder
     * @param string          $url
     * @param string          $folderEvents
     *
     * @return Response
     */
    protected function generateForm(Request $request, FolderInterface $folder, $url, $folderEvents)
    {
        $form = $this->createForm('folder', $folder, array('action' => $url));
        $form->handleRequest($request);
        $this->handleForm(
            $form,
            $this->get('translator')->trans('php_orchestra_backoffice.form.folder.success'),
            $folder
        );

        $this->dispatchEvent($folderEvents, new FolderEvent($folder));

        return $this->renderAdminForm($form);
    }
}
