<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ContentController
 */
class ContentController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $contentId
     *
     * @Config\Route("/content/form/{contentId}", name="php_orchestra_backoffice_content_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $contentId)
    {
        $language = $request->get('language');

        if($language === null){
            $contextManager = $this->get('php_orchestra_backoffice.context_manager');
            $language = $contextManager->getCurrentLocale();
        }

        $content = $this->get('php_orchestra_model.repository.content')->findOneBy(array('contentId' => $contentId, 'language' => $language));
        if($content === null){
            $contentSource = $this->get('php_orchestra_model.repository.content')->findOneByContentId($contentId);
            $contentClass = $this->container->getParameter('php_orchestra_model.document.content.class');
            $content = new $contentClass();
            $content->setContentId($contentId);
            $content->setContentType($contentSource->getContentType());
            $content->setLanguage($language);
        }
        $form = $this->createForm('orchestra_content', $content, array(
            'action' => $this->generateUrl('php_orchestra_backoffice_content_form', array(
                'contentId' => $contentId,
            ))
        ));

        $form->handleRequest($request);

        $this->handleForm(
            $form,
            $this->get('translator')->trans('php_orchestra_backoffice.form.content.success'),
            $content
        );

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/content/new/{contentType}", name="php_orchestra_backoffice_content_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request, $contentType)
    {
        $contentClass = $this->container->getParameter('php_orchestra_model.document.content.class');
        $content = new $contentClass();
        $content->setContentType($contentType);
        $content->setLanguage($this->get('php_orchestra.manager.current_site')->getCurrentSiteDefaultLanguage());

        $form = $this->createForm('orchestra_content', $content, array(
            'action' => $this->generateUrl('php_orchestra_backoffice_content_new', array(
                'contentType' => $contentType
            )),
            'method' => 'POST',
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($content);
            $documentManager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('php_orchestra_backoffice.form.content.creation')
            );

            return $this->redirect(
                $this->generateUrl('php_orchestra_backoffice_content_form', array(
                    'contentId' => $content->getId()
                ))
            );
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
