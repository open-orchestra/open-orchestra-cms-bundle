<?php

namespace OpenOrchestra\MediaAdminBundle\Controller\Admin;

use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\Media\Event\FolderEvent;
use OpenOrchestra\Media\FolderEvents;
use OpenOrchestra\Media\Model\FolderInterface;
use Symfony\Component\Form\Form;
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
     * @Config\Route("/folder/form/{folderId}", name="open_orchestra_media_admin_folder_form")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_TREE_FOLDER')")
     *
     * @return Response
     */
    public function formAction(Request $request, $folderId)
    {
        $folderRepository = $this->get('open_orchestra_media.repository.media_folder');
        $folder = $folderRepository->find($folderId);

        $url = $this->generateUrl('open_orchestra_media_admin_folder_form', array('folderId' => $folderId));
        $message = $this->get('translator')->trans('open_orchestra_media_admin.form.folder.success');

        $form = $this->generateForm($folder, $url);
        $form->handleRequest($request);

        if ($this->handleForm($form, $message, $folder)) {
            $this->dispatchEvent(FolderEvents::FOLDER_UPDATE, new FolderEvent($folder));
        }


        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $parentId
     *
     * @Config\Route("/folder/new/{parentId}", name="open_orchestra_media_admin_folder_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_TREE_FOLDER')")
     *
     * @return Response
     */
    public function newAction(Request $request, $parentId)
    {
        $parentFolder = $this->container->get('open_orchestra_media.repository.media_folder')->find($parentId);
        $folderClass = $this->container->getParameter('open_orchestra_media.document.media_folder.class');
        /** @var FolderInterface $folder */
        $folder = new $folderClass();
        if ($parentFolder) {
            $folder->setParent($parentFolder);
        }

        $url = $this->generateUrl('open_orchestra_media_admin_folder_new', array('parentId' => $parentId));
        $message = $this->get('translator')->trans('open_orchestra_media_admin.form.folder.success');

        $form = $this->generateForm($folder, $url);
        $form->handleRequest($request);

        if ($this->handleForm($form, $message, $folder)) {
            $url = $this->generateUrl('open_orchestra_media_admin_folder_form', array('folderId' => $folder->getId()));
            $this->dispatchEvent(FolderEvents::FOLDER_UPDATE, new FolderEvent($folder));

            return $this->redirect($url);
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param FolderInterface $folder
     * @param string          $url
     *
     * @return Form
     */
    protected function generateForm(FolderInterface $folder, $url)
    {
        $form = $this->createForm('folder', $folder, array('action' => $url));

        return $form;
    }
}
