<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MediaController
 */
class MediaController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $folderId
     *
     * @Config\Route("/media/new/{folderId}", name="php_orchestra_backoffice_media_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request, $folderId)
    {
        $folderRepository = $this->get('php_orchestra_model.repository.media_folder');
        $folder = $folderRepository->find($folderId);

        $mediaClass = $this->container->getParameter('php_orchestra_model.document.media.class');
        $media = new $mediaClass();
        $media->setMediaFolder($folder);

        $form = $this->createForm('media', $media, array(
            'action' => $this->generateUrl('php_orchestra_backoffice_media_new', array(
                'folderId' => $folderId,
            ))
        ));

        $form->handleRequest($request);

        $this->handleForm(
            $form,
            $this->get('translator')->trans('php_orchestra_backoffice.form.media.success'),
            $media
        );

        return $this->renderAdminForm($form);
    }

    /**
     * @Config\Route("/media/list/folders", name="php_orchestra_backoffice_media_list_form")
     * @Config\Method({"GET"})
     *
     * @return Response
     */
    public function showFolders()
    {
        $rootFolders = $this->get('php_orchestra_model.repository.media_folder')->findAllRootFolder();

        return $this->render( 'PHPOrchestraBackofficeBundle:Tree:showModalFolderTree.html.twig', array(
                'folders' => $rootFolders,
        ));
    }
}
