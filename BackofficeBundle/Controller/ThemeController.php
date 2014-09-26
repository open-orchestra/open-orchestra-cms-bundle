<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ThemeController
 */
class ThemeController extends Controller
{
    /**
     * @param Request $request
     * @param int     $themeId
     *
     * @Config\Route("/theme/form/{themeId}", name="php_orchestra_backoffice_theme_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $themeId)
    {
        $theme = $this->get('php_orchestra_model.repository.theme')->find($themeId);

        $form = $this->createForm(
            'theme',
            $theme,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_theme_form', array(
                    'themeId' => $themeId,
                ))
            )
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($theme);
            $documentManager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('php_orchestra_backoffice.form.theme.success')
            );
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/theme/new", name="php_orchestra_backoffice_theme_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $themeClass = $this->container->getParameter('php_orchestra_model.document.theme.class');
        $theme = new $themeClass();
        $form = $this->createForm(
            'theme',
            $theme,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_theme_new'),
                'method' => 'POST',
            )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($theme);
            $documentManager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('php_orchestra_backoffice.form.theme.creation')
            );

            return $this->redirect(
                $this->generateUrl('php_orchestra_backoffice_theme_form', array(
                    'themeId' => $theme->getId(),
                ))
            );
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
