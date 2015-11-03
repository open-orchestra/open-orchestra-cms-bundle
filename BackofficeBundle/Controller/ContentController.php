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
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_CONTENT_TYPE_FOR_CONTENT')")
     *
     * @return Response
     */
    public function formAction(Request $request, $contentId)
    {
        $language = $request->get(
            'language',
            $this->get('open_orchestra_backoffice.context_manager')->getDefaultLocale()
        );
        $version = $request->get('version');

        $content = $this->get('open_orchestra_model.repository.content')->findOneByLanguageAndVersion($contentId, $language, $version);

        $form = $this->createForm('orchestra_content', $content, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_content_form', array(
                'contentId' => $content->getContentId(),
                'language' => $content->getLanguage(),
                'version' => $content->getVersion(),
            ))
        ));

        $form->handleRequest($request);
        $message =  $this->get('translator')->trans('open_orchestra_backoffice.form.content.success');

        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(ContentEvents::CONTENT_UPDATE, new ContentEvent($content));
        }

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
     * @param string  $contentType
     *
     * @Config\Route("/content/new/{contentType}", name="open_orchestra_backoffice_content_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_CREATE_CONTENT_TYPE_FOR_CONTENT')")
     *
     * @return Response
     */
    public function newAction(Request $request, $contentType)
    {
        $content = $this->get('open_orchestra_backoffice.manager.content')->initializeNewContent($contentType);

        $form = $this->createForm('orchestra_content', $content, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_content_new', array(
                'contentType' => $contentType
            )),
            'method' => 'POST',
        ));

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.content.creation');

        if ($this->handleForm($form, $message, $content)) {
            $this->dispatchEvent(ContentEvents::CONTENT_CREATION, new ContentEvent($content));
            $response = new Response('', Response::HTTP_CREATED, array('Content-type' => 'text/html; charset=utf-8'));

            return $this->render('BraincraftedBootstrapBundle::flash.html.twig', array(), $response);
        }

        return $this->render(
            $this->getFormTemplate($contentType),
            array('form' => $form->createView())
        );
    }
}
