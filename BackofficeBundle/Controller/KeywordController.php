<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\ModelBundle\Document\Keyword;
use PHPOrchestra\ModelBundle\Model\KeywordInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class KeywordController
 */
class KeywordController extends Controller
{
    /**
     * @param Request $request
     * @param int     $keywordId
     *
     * @Config\Route("/keyword/form/{keywordId}", name="php_orchestra_backoffice_keyword_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $keywordId)
    {
        $keyword = $this->get('php_orchestra_model.repository.keyword')->find($keywordId);

        $url = $this->generateUrl('php_orchestra_backoffice_keyword_form', array('keywordId' => $keywordId));
        $message = $this->get('translator')->trans('php_orchestra_backoffice.form.keyword.success');

        return $this->formHandler($url, $request, $keyword, $message);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/keyword/new", name="php_orchestra_backoffice_keyword_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $keywordClass = $this->container->getParameter('php_orchestra_model.document.keyword.class');
        $keyword = new $keywordClass();

        $url = $this->generateUrl('php_orchestra_backoffice_keyword_new');
        $message = $this->get('translator')->trans('php_orchestra_backoffice.form.keyword.creation');

        return $this->formHandler($url, $request, $keyword, $message);
    }

    /**
     * @param String             $url
     * @param Request            $request
     * @param KeywordInterface   $keyword
     * @param String             $message
     *
     * @return Response
     */
    protected function formHandler($url, Request $request, KeywordInterface $keyword, $message)
    {
        $form = $this->createForm(
            'keyword',
            $keyword,
            array(
                'action' => $url,
            )
        );
        $form->handleRequest($request);
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($keyword);
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
