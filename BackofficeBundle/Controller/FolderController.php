<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\ModelBundle\Model\FolderInterface;
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
     * @param string  $parentId
     *
     * @Config\Route("/folder/new/{parentId}", name="php_orchestra_backoffice_folder_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request, $parentId)
    {
        $parentFolder = $this->container->get('php_orchestra_model.repository.media_folder')->find($parentId);

        $folderClass = $this->container->getParameter('php_orchestra_model.document.media_folder.class');
        $folder = new $folderClass();
        if($parentFolder !== null){
            $folder->setParent($parentFolder);
        }

        $form = $this->createForm('folder', $folder, array(
            'action' => $this->generateUrl('php_orchestra_backoffice_folder_new', array(
                'parentId' => $parentId,
            ))
        ));

        $form->handleRequest($request);

        $this->handleForm(
            $form,
            $this->get('translator')->trans('php_orchestra_backoffice.form.folder.success'),
            $folder
        );

        return $this->renderAdminForm($form);
    }
}
