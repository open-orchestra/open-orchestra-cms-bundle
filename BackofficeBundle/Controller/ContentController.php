<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Exception\UneditableException;

/**
 * Class ContentController
 */
class ContentController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $contentId
     *
     * @Config\Route("/content/form/{contentId}/{language}", name="open_orchestra_backoffice_content_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $contentId, $language)
    {
        $version = $request->get('version');
        $content = $this->get('open_orchestra_model.repository.content')->findOneByLanguageAndVersion($contentId, $language, $version);
        if (!$content instanceof ContentInterface) {
            throw new \UnexpectedValueException();
        }
        $contentType = $this->get('open_orchestra_model.repository.content_type')->findOneByContentTypeIdInLastVersion($content->getContentType());
        if (!$contentType instanceof ContentTypeInterface) {
            throw new \UnexpectedValueException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $content);

        $currentlyPublishedContents = $this->get('open_orchestra_model.repository.content')->findAllCurrentlyPublishedByContentId($contentId);
        $isUsed = false;
        foreach ($currentlyPublishedContents as $currentlyPublishedContent) {
            $isUsed = $isUsed || $currentlyPublishedContent->isUsed();
        }
        $form = $this->createForm('oo_content', $content, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_content_form', array(
                'contentId' => $content->getContentId(),
                'language' => $content->getLanguage(),
                'version' => $content->getVersion(),
            )),
            'delete_button' => ($this->isGranted(ContributionActionInterface::DELETE, $content) && !$isUsed),
            'need_link_to_site_defintion' => false,
            'is_blocked_edition' => $content->getStatus() ? $content->getStatus()->isBlockedEdition() : false,
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
     * @param Request $request
     * @param string  $contentTypeId
     *
     * @Config\Route("/content/new/{contentTypeId}/{language}", name="open_orchestra_backoffice_content_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request, $contentTypeId, $language)
    {
        $contentManager = $this->get('open_orchestra_backoffice.manager.content');
        $contentType = $this->get('open_orchestra_model.repository.content_type')->findOneByContentTypeIdInLastVersion($contentTypeId);
        if (!$contentType instanceof ContentTypeInterface) {
            throw new \UnexpectedValueException();
        }
        $content = $contentManager->initializeNewContent($contentTypeId, $language, $contentType->isLinkedToSite() && $contentType->isAlwaysShared());
        if (!$content instanceof ContentInterface) {
            throw new \UnexpectedValueException();
        }
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, $content);

        $form = $this->createForm('oo_content', $content, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_content_new', array(
                'contentTypeId' => $contentTypeId,
                'language' => $language,
            )),
            'method' => 'POST',
            'new_button' => true,
            'need_link_to_site_defintion' => $contentType->isLinkedToSite() && !$contentType->isAlwaysShared(),
            'is_blocked_edition' => $content->getStatus() ? $content->getStatus()->isBlockedEdition() : false,
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $contentsEvent = array();
            $documentManager = $this->get('object_manager');
            $documentManager->persist($content);
            $contentsEvent[] = new ContentEvent($content);

            $languages = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteLanguages();
            foreach ($languages as $siteLanguage) {
                if ($language !== $siteLanguage) {
                    $translatedContent = $contentManager->createNewLanguageContent($content, $siteLanguage);
                    $documentManager->persist($translatedContent);
                    $contentsEvent[] = new ContentEvent($translatedContent);
                }
            }
            $documentManager->flush();

            foreach ($contentsEvent as $contentEvent) {
                $this->dispatchEvent(ContentEvents::CONTENT_CREATION, $contentEvent);
            }
            $message = $this->get('translator')->trans('open_orchestra_backoffice.form.content.creation');
            $this->get('session')->getFlashBag()->add('success', $message);
            $response = new Response(
                '',
                Response::HTTP_CREATED,
                array('Content-type' => 'text/plain; charset=utf-8', 'contentId' => $content->getContentId(), 'name' => $content->getName())
            );

            return $response;
        }

        return $this->render('OpenOrchestraBackofficeBundle::form.html.twig', array(
            'form' => $form->createView()
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

        if ($contentType instanceof ContentTypeInterface) {
            $customTemplate = $contentType->getTemplate();

            if ($customTemplate != '' && $this->get('templating')->exists($customTemplate)) {
                $template = $customTemplate;
            }
        }

        return $template;
    }
}
