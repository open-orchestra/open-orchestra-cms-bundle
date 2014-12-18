<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\ModelInterface\Model\ContentTypeInterface;
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
     * @Config\Route("/content-type/form/{contentTypeId}", name="php_orchestra_backoffice_content_type_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $contentTypeId)
    {
        $contentType = $this->get('php_orchestra_model.repository.content_type')->findOneByContentTypeIdAndVersion($contentTypeId);
        $newContentType = $this->get('php_orchestra_backoffice.manager.content_type')->duplicate($contentType);

        $form = $this->createForm(
            'content_type',
            $newContentType,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_content_type_form', array(
                        'contentTypeId' => $contentTypeId,
                    )),
                'method' => 'POST'
            )
        );

        $form->handleRequest($request);
        if (!$request->get('no_save')) {
            $this->handleForm($form, $this->get('translator')->trans('php_orchestra_backoffice.form.content_type.success'), $newContentType);
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/content-type/new", name="php_orchestra_backoffice_content_type_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $contentTypeClass = $this->container->getParameter('php_orchestra_model.document.content_type.class');
        /** @var ContentTypeInterface $contentType */
        $contentType = new $contentTypeClass();

        $form = $this->createForm(
            'content_type',
            $contentType,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_content_type_new', array())
            )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($contentType);
            $documentManager->flush();

            return $this->redirect(
                $this->generateUrl('homepage')
            );
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
