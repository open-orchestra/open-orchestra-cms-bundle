<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\Event\ThemeEvent;
use OpenOrchestra\ModelInterface\ThemeEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ThemeController
 */
class ThemeController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param int     $themeId
     *
     * @Config\Route("/theme/form/{themeId}", name="open_orchestra_backoffice_theme_form")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_THEME')")
     *
     * @return Response
     */
    public function formAction(Request $request, $themeId)
    {
        $theme = $this->get('open_orchestra_model.repository.theme')->find($themeId);

        $form = $this->createForm(
            'theme',
            $theme,
            array(
                'action' => $this->generateUrl('open_orchestra_backoffice_theme_form', array(
                    'themeId' => $themeId,
                ))
            )
        );

        $form->handleRequest($request);

        $this->handleForm(
            $form,
            $this->get('translator')->trans('open_orchestra_backoffice.form.theme.success'),
            $theme
        );

        $this->dispatchEvent(ThemeEvents::THEME_UPDATE, new ThemeEvent($theme));

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/theme/new", name="open_orchestra_backoffice_theme_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_THEME')")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $themeClass = $this->container->getParameter('open_orchestra_model.document.theme.class');
        $theme = new $themeClass();
        $form = $this->createForm(
            'theme',
            $theme,
            array(
                'action' => $this->generateUrl('open_orchestra_backoffice_theme_new'),
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
                $this->get('translator')->trans('open_orchestra_backoffice.form.theme.creation')
            );

            $this->dispatchEvent(ThemeEvents::THEME_CREATE, new ThemeEvent($theme));

            return $this->redirect(
                $this->generateUrl('open_orchestra_backoffice_theme_form', array(
                    'themeId' => $theme->getId(),
                ))
            );
        }

        return $this->renderAdminForm($form);
    }
}
