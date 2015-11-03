<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\ContentTypeEvents;
use OpenOrchestra\ModelInterface\Event\ContentTypeEvent;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;

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
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_CONTENT_TYPE')")
     *
     * @return Response
     */
    public function formAction(Request $request, $contentTypeId)
    {
        $contentType = $this->get('open_orchestra_model.repository.content_type')->findOneByContentTypeIdInLastVersion($contentTypeId);
        $newContentType = $this->get('open_orchestra_backoffice.manager.content_type')->duplicate($contentType);

        $action = $this->generateUrl('open_orchestra_backoffice_content_type_form', array('contentTypeId' => $contentTypeId));
        $form = $this->createContentTypeForm($request, array('action' => $action), $newContentType);

        $form->handleRequest($request);
        if ('PATCH' !== $request->getMethod()) {
            $this->handleForm($form, $this->get('translator')->trans('open_orchestra_backoffice.form.content_type.success'), $newContentType);
            $this->dispatchEvent(ContentTypeEvents::CONTENT_TYPE_UPDATE, new ContentTypeEvent($newContentType));
        }

        return $this->render('OpenOrchestraBackofficeBundle::form.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/content-type/new", name="open_orchestra_backoffice_content_type_new")
     * @Config\Method({"GET", "POST", "PATCH"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_CREATE_CONTENT_TYPE')")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        /** @var ContentTypeInterface $contentType */
        $contentType = $this->get('open_orchestra_backoffice.manager.content_type')->initializeNewContentType();

        $action = $this->generateUrl('open_orchestra_backoffice_content_type_new', array());
        $form = $this->createContentTypeForm($request, array('action' => $action), $contentType);

        $form->handleRequest($request);
        if ('PATCH' !== $request->getMethod()) {
            $handleForm = $this->handleForm($form, $this->get('translator')->trans('open_orchestra_backoffice.form.content_type.creation'), $contentType);

            if ($handleForm && !is_null($contentType->getId())) {
                $this->dispatchEvent(ContentTypeEvents::CONTENT_TYPE_CREATE, new ContentTypeEvent($contentType));
                $response = new Response('', Response::HTTP_CREATED, array('Content-type' => 'text/html; charset=utf-8'));

                return $this->render('BraincraftedBootstrapBundle::flash.html.twig', array(), $response);
            }
        }

        return $this->render('OpenOrchestraBackofficeBundle::form.html.twig', array(
            'form' => $form->createView()
        ));
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

        return $this->createForm('content_type', $contentType, $option);
    }
}
