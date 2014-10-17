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
     * @param int     $folderId
     *
     * @Config\Route("/folder/form/{folderId}", name="php_orchestra_backoffice_folder_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $folderId)
    {
        $folderRepository = $this->container->get('php_orchestra_model.repository.media_folder');
        $folder = $folderRepository->findOneById($folderId);

        $url = $this->generateUrl('php_orchestra_backoffice_form_new', array('parent_id' => $parent_id));
        $message = $this->get('translator')->trans('php_orchestra_backoffice.form.form.success');
        $form = $this->generateForm($folder, $url);
        $form->handleRequest($request);
        $this->handleForm($form, $message, $folder);

        if ($form->getErrors()->count() > 0) {
            $statusCode = 400;
        } elseif (!is_null($folder->getId())) {
            $url = $this->generateUrl('php_orchestra_backoffice_folder_form', array('folderId' => $folder->getId()));
            return $this->redirect($url);
        } else {
            $statusCode = 200;
        };

        $response = new Response('', $statusCode, array('Content-type' => 'text/html; charset=utf-8'));

        return $this->render(
            'PHPOrchestraBackofficeBundle:Editorial:template.html.twig',
            array('form' => $form->createView()),
            $response
        );
    }

    /**
     * @param Request $request
     * @param string  $parent_id
     *
     * @Config\Route("/folder/new/{parent_id}", name="php_orchestra_backoffice_folder_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request, $parent_id)
    {
        $parentFolder = $this->container->get('php_orchestra_model.repository.media_folder')->findOneById($parent_id);
        $folderClass = $this->container->getParameter('php_orchestra_model.document.media_folder.class');
        $folder = new $folderClass();
        $folder->setParent($parentFolder);

        $form = $this->createForm('folder', $folder, array(
            'action' => $this->generateUrl('php_orchestra_backoffice_folder_new', array(
                'parent_id' => $parent_id,
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

    /**
     * @param FolderInterface $folder
     * @param string          $url
     *
     * @return Form
     */
    protected function generateForm(FolderInterface $folder, $url)
    {
        $form = $this->createForm(
            'folder',
            $folder,
            array(
                'action' => $url
            )
        );

        return $form;
    }
}
