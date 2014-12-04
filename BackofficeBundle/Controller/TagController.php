<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\ModelBundle\Document\Tag;
use PHPOrchestra\ModelBundle\Model\TagInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TagController
 */
class TagController extends Controller
{
    /**
     * @param Request $request
     * @param int     $tagId
     *
     * @Config\Route("/tag/form/{tagId}", name="php_orchestra_backoffice_tag_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $tagId)
    {
        $tag = $this->get('php_orchestra_model.repository.tag')->find($tagId);

        $url = $this->generateUrl('php_orchestra_backoffice_tag_form', array('tagId' => $tagId));
        $message = $this->get('translator')->trans('php_orchestra_backoffice.form.tag.success');

        return $this->formHandler($url, $request, $tag, $message);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/tag/new", name="php_orchestra_backoffice_tag_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $tagClass = $this->container->getParameter('php_orchestra_model.document.tag.class');
        $tag = new $tagClass();

        $url = $this->generateUrl('php_orchestra_backoffice_tag_new');
        $message = $this->get('translator')->trans('php_orchestra_backoffice.form.tag.creation');

        return $this->formHandler($url, $request, $tag, $message);
    }

    /**
     * @param String         $url
     * @param Request        $request
     * @param TagInterface   $tag
     * @param String         $message
     *
     * @return Response
     */
    protected function formHandler($url, Request $request, TagInterface $tag, $message)
    {
        $form = $this->createForm(
            'tag',
            $tag,
            array(
                'action' => $url,
            )
        );
        $form->handleRequest($request);
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($tag);
            $documentManager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $message
            );
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
