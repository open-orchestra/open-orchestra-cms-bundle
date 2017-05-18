<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\ContentTypeEvents;
use OpenOrchestra\ModelInterface\Event\ContentTypeEvent;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class ContentTypeController
 */
class ContentTypeController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $contentTypeId
     *
     * @Config\Route("/content-type/form/{contentTypeId}", name="open_orchestra_backoffice_content_type_form")
     * @Config\Method({"GET", "POST", "PATCH"})
     *
     * @return Response
     */
    public function formAction(Request $request, $contentTypeId)
    {
        $contentType = $this->get('open_orchestra_model.repository.content_type')->findOneByContentTypeIdInLastVersion($contentTypeId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $contentType);

        $newContentType = $this->get('open_orchestra_backoffice.manager.content_type')->duplicate($contentType);
        $action = $this->generateUrl('open_orchestra_backoffice_content_type_form', array('contentTypeId' => $contentTypeId));
        $form = $this->createContentTypeForm($request, array(
            'action' => $action,
            'delete_button' => ($this->isGranted(ContributionActionInterface::DELETE, $newContentType) && 0 == $this->get('open_orchestra_model.repository.content')->countByContentType($contentTypeId)),
            'need_link_to_site_defintion' => false,
        ), $newContentType);

        $form->handleRequest($request);
        if ('PATCH' !== $request->getMethod()) {
            if ($form->isValid()) {
                $newContentType->setVersion(((int)$newContentType->getVersion()) + 1);
                $documentManager = $this->get('object_manager');
                $documentManager->persist($newContentType);
                $documentManager->flush();

                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('open_orchestra_backoffice.form.content_type.success'));
                $this->dispatchEvent(ContentTypeEvents::CONTENT_TYPE_UPDATE, new ContentTypeEvent($newContentType));
            }
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/content-type/new", name="open_orchestra_backoffice_content_type_new")
     * @Config\Method({"GET", "POST", "PATCH"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        /** @var ContentTypeInterface $contentType */
        $contentType = $this->get('open_orchestra_backoffice.manager.content_type')->initializeNewContentType();

        $action = $this->generateUrl('open_orchestra_backoffice_content_type_new', array());
        $form = $this->createContentTypeForm($request, array(
            'action' => $action,
            'new_button' => true,
            'need_link_to_site_defintion' => true,
        ), $contentType);

        $form->handleRequest($request);
        if ('PATCH' !== $request->getMethod()) {
            if ($form->isValid()) {
                $contentType->setVersion(1);
                $language = $this->get('open_orchestra_backoffice.context_backoffice_manager')->getBackOfficeLanguage();
                $documentManager = $this->get('object_manager');
                $documentManager->persist($contentType);
                $documentManager->flush();
                $message = $this->get('translator')->trans('open_orchestra_backoffice.form.content_type.creation');
                $this->dispatchEvent(ContentTypeEvents::CONTENT_TYPE_CREATE, new ContentTypeEvent($contentType));
                $response = new Response(
                    $message,
                    Response::HTTP_CREATED,
                    array('Content-type' => 'text/plain; charset=utf-8', 'contentTypeId' => $contentType->getContentTypeId(), 'name' => $contentType->getName($language))
                );

                return $response;
            }
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request              $request
     * @param string               $option
     * @param ContentTypeInterface $contentType
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createContentTypeForm(Request $request, $option, ContentTypeInterface $contentType)
    {
        $method = "POST";
        if ("PATCH" === $request->getMethod()) {
            $option["validation_groups"] = false;
            $method = "PATCH";
        }
        $option["method"] = $method;

        return $this->createForm('oo_content_type', $contentType, $option);
    }
}
