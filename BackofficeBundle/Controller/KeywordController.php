<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\Event\KeywordEvent;
use OpenOrchestra\ModelInterface\KeywordEvents;
use OpenOrchestra\ModelInterface\Model\KeywordInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class KeywordController
 */
class KeywordController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param int     $keywordId
     *
     * @Config\Route("/keyword/form/{keywordId}", name="open_orchestra_backoffice_keyword_form")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_PANEL_KEYWORD')")
     *
     * @return Response
     */
    public function formAction(Request $request, $keywordId)
    {
        $keyword = $this->get('open_orchestra_model.repository.keyword')->find($keywordId);

        $form = $this->createForm(
            'keyword',
            $keyword,
            array(
                'action' => $this->generateUrl('open_orchestra_backoffice_keyword_form', array('keywordId' => $keywordId)),
            )
        );

        $form->handleRequest($request);
        $this->handleForm($form, $this->get('translator')->trans('open_orchestra_backoffice.form.keyword.success'), $keyword);

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/keyword/new", name="open_orchestra_backoffice_keyword_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_PANEL_KEYWORD')")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $keywordClass = $this->container->getParameter('open_orchestra_model.document.keyword.class');
        /** @var KeywordInterface $keyword */
        $keyword = new $keywordClass();

        $form = $this->createForm(
            'keyword',
            $keyword,
            array(
                'action' => $this->generateUrl('open_orchestra_backoffice_keyword_new'),
            )
        );

        $form->handleRequest($request);
        $this->handleForm($form, $this->get('translator')->trans('open_orchestra_backoffice.form.keyword.creation'), $keyword);

        $statusCode = 200;
        if ($form->getErrors()->count() > 0) {
            $statusCode = 400;
        } elseif (!is_null($keyword->getId())) {
            $url = $this->generateUrl('open_orchestra_backoffice_keyword_form', array('keywordId' => $keyword->getId()));

            $this->dispatchEvent(KeywordEvents::KEYWORD_CREATE, new KeywordEvent($keyword));
            return $this->redirect($url);
        };

        $response = new Response('', $statusCode, array('Content-type' => 'text/html; charset=utf-8'));


        return $this->renderAdminForm($form, array(), $response);
    }
}
