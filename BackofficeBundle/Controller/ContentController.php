<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\Backoffice\BusinessRules\Strategies\BusinessActionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class ContentController
 */
class ContentController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $contentId
     * @param string  $language
     * @param string  $version
     *
     * @Config\Route(
     *     "/content/form/{contentId}/{language}/{version}",
     *      name="open_orchestra_backoffice_content_form",
     *      defaults={"version": null},
     * )
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $contentId, $language, $version)
    {
        $content = $this->get('open_orchestra_model.repository.content')->findOneByLanguageAndVersion($contentId, $language, $version);
        if (!$content instanceof ContentInterface) {
            throw new \UnexpectedValueException();
        }
        $contentType = $this->get('open_orchestra_model.repository.content_type')->findOneByContentTypeIdInLastVersion($content->getContentType());
        if (!$contentType instanceof ContentTypeInterface &&
            !$this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(BusinessActionInterface::EDIT, $content)
        ) {
            throw new \UnexpectedValueException();
        }

        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $content);

        $publishedContents = $this->get('open_orchestra_model.repository.content')->findAllPublishedByContentId($contentId);
        $isUsed = false;
        foreach ($publishedContents as $publishedContent) {
            $isUsed = $isUsed || $publishedContent->isUsed();
        }
        $options = array(
            'action' => $this->generateUrl('open_orchestra_backoffice_content_form', array(
                'contentId' => $content->getContentId(),
                'language' => $content->getLanguage(),
                'version' => $content->getVersion(),
            )),
            'delete_button' => $this->isGranted(ContributionActionInterface::DELETE, $content) &&
                $this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(BusinessActionInterface::DELETE, $content),
            'need_link_to_site_defintion' => false,
            'disabled' => $content->getStatus() ? $content->getStatus()->isBlockedEdition() : false,
            'is_statusable' => $contentType->isDefiningStatusable()
        );
        $form = $this->createForm('oo_content', $content, $options);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('object_manager')->flush();
            $this->dispatchEvent(ContentEvents::CONTENT_UPDATE, new ContentEvent($content));

            $message =  $this->get('translator')->trans('open_orchestra_backoffice.form.content.success');
            $this->get('session')->getFlashBag()->add('success', $message);

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
     * @param string  $language
     *
     * @Config\Route("/content/new/{contentTypeId}/{language}", name="open_orchestra_backoffice_content_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request, $contentTypeId, $language)
    {
        $siteId = $this->get('open_orchestra_backoffice.context_backoffice_manager')->getSiteId();
        $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);

        $contentManager = $this->get('open_orchestra_backoffice.manager.content');
        $contentType = $this->get('open_orchestra_model.repository.content_type')->findOneByContentTypeIdInLastVersion($contentTypeId);
        if (!$contentType instanceof ContentTypeInterface || !in_array($contentType->getContentTypeId(), $site->getContentTypes())) {
            throw new \UnexpectedValueException();
        }
        $content = $contentManager->initializeNewContent(
            $contentTypeId,
            $language,
            $contentType->isLinkedToSite() && $contentType->isAlwaysShared(),
            $contentType->isDefiningStatusable()
        );
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
            'is_statusable' => $contentType->isDefiningStatusable()
        ));

        $status = $content->getStatus();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $content = $contentManager->setVersionName($content);
            $documentManager = $this->get('object_manager');
            $documentManager->persist($content);
            $this->dispatchEvent(ContentEvents::CONTENT_CREATION, new ContentEvent($content));

            $this->createContentInNewLanguage($content, $language);

            $documentManager->flush();
            if ($status->getId() !== $content->getStatus()->getId()) {
                $this->dispatchEvent(ContentEvents::CONTENT_CHANGE_STATUS, new ContentEvent($content, $status));
            }

            $message = $this->get('translator')->trans('open_orchestra_backoffice.form.content.creation');
            $response = new Response(
                $message,
                Response::HTTP_CREATED,
                array('Content-type' => 'text/plain; charset=utf-8', 'contentId' => $content->getContentId(), 'version' => $content->getVersion())
            );

            return $response;
        }

        return $this->renderAdminForm($form);
    }

    /**
     * Get Form Template related to content of $contentTypeId
     *
     * @param string $contentTypeId
     *fof
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

    /**
     * @param ContentInterface $content
     * @param string           $currentLanguage
     */
    protected function createContentInNewLanguage(ContentInterface $content, $currentLanguage)
    {
        $languages = $this->get('open_orchestra_backoffice.context_backoffice_manager')->getSiteLanguages();
        foreach ($languages as $siteLanguage) {
            if ($currentLanguage !== $siteLanguage) {
                $translatedContent = $this->get('open_orchestra_backoffice.manager.content')->createNewLanguageContent($content, $siteLanguage);
                $this->get('object_manager')->persist($translatedContent);
                $this->dispatchEvent(ContentEvents::CONTENT_CREATION, new ContentEvent($translatedContent));
            }
        }
    }
}
