<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\ContentEvents;
use OpenOrchestra\ModelInterface\Event\ContentEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     *
     * @Config\Route("/content/form/{contentId}/{language}", name="open_orchestra_backoffice_content_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $contentId, $language)
    {
        $repository = $this->get('open_orchestra_model.repository.content');
        $version = $request->get('version');

        $content = $repository->findOneByLanguageAndVersion($contentId, $language, $version);
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $content);

        if ($content instanceof ContentInterface) {
            $currentlyPublishedContents = $repository->findAllCurrentlyPublishedByContentId($contentId);
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
                'delete_button' => ($this->isGranted(ContributionActionInterface::DELETE, $content) && !$isUsed)
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
        $content = $this->get('open_orchestra_backoffice.manager.content')->initializeNewContent($contentTypeId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, $content);

        $form = $this->createForm('oo_content', $content, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_content_new', array(
                'contentTypeId' => $contentTypeId,
                'language' => $language,
            )),
            'method' => 'POST',
            'new_button' => true
        ), ContributionActionInterface::CREATE);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('object_manager');
            $documentManager->persist($content);
            $documentManager->flush();
            $message = $this->get('translator')->trans('open_orchestra_backoffice.form.content.creation');
            $this->get('session')->getFlashBag()->add('success', $message);

            $this->dispatchEvent(ContentEvents::CONTENT_CREATION, new ContentEvent($content, null));
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
