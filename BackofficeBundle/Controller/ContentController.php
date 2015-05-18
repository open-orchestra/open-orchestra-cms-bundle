<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
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
     * @Config\Route("/content/form/{contentId}", name="open_orchestra_backoffice_content_form")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT')")
     *
     * @return Response
     */
    public function formAction(Request $request, $contentId)
    {
        $language = $request->get(
            'language',
            $this->get('open_orchestra.manager.current_site')->getCurrentSiteDefaultLanguage()
        );
        $version = $request->get('version');

        $content = $this->get('open_orchestra_model.repository.content')->findOneByContentIdAndLanguageAndVersion($contentId, $language, $version);

        $form = $this->createForm('orchestra_content', $content, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_content_form', array(
                'contentId' => $content->getContentId(),
                'language' => $content->getLanguage(),
                'version' => $content->getVersion(),
            ))
        ));

        $form->handleRequest($request);

        $this->handleForm(
            $form,
            $this->get('translator')->trans('open_orchestra_backoffice.form.content.success'),
            $content
        );

        $this->dispatchEvent(ContentEvents::CONTENT_UPDATE, new ContentEvent($content));

        return $this->renderAdminForm(
            $form,
            array(),
            null,
            $this->getFormTemplate($content->getContentType()
        ));
    }

    /**
     * Get Form Template related to content of $contentTypeId
     * 
     * @param string $contentTypeId
     * 
     * @return string
     */
    protected function getFormTemplate($contentTypeId)
    {
        $template = AbstractAdminController::TEMPLATE;

        $contentType = $this->get('open_orchestra_model.repository.content_type')->findOneByContentTypeIdInLastVersion($contentTypeId);

        $customTemplate = $contentType->getTemplate();

        if ($customTemplate != '' && $this->get('templating')->exists($customTemplate)) {
            $template = $customTemplate;
        }

        return $template;
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/content/new/{contentType}", name="open_orchestra_backoffice_content_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_CONTENT_TYPE_FOR_CONTENT')")
     *
     * @return Response
     */
    public function newAction(Request $request, $contentType)
    {
        $contentClass = $this->container->getParameter('open_orchestra_model.document.content.class');
        $content = new $contentClass();
        $content->setContentType($contentType);
        $content->setLanguage($this->get('open_orchestra.manager.current_site')->getCurrentSiteDefaultLanguage());

        $form = $this->createForm('orchestra_content', $content, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_content_new', array(
                'contentType' => $contentType
            )),
            'method' => 'POST',
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($content);
            $documentManager->flush();

            $this->dispatchEvent(ContentEvents::CONTENT_CREATION, new ContentEvent($content));

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('open_orchestra_backoffice.form.content.creation')
            );

            return $this->redirect(
                $this->generateUrl('open_orchestra_backoffice_content_form', array(
                    'contentId' => $content->getContentId()
                ))
            );
        }

        return $this->render(
            $this->getFormTemplate($contentType),
            array('form' => $form->createView())
        );
    }
}
