<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\ModelInterface\Event\SiteEvent;
use PHPOrchestra\ModelInterface\SiteEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SiteController
 */
class SiteController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $siteId
     *
     * @Config\Route("/site/form/{siteId}", name="php_orchestra_backoffice_site_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $siteId)
    {
        $site = $this->get('php_orchestra_model.repository.site')->findOneBySiteId($siteId);

        $form = $this->createForm(
            'site',
            $site,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_site_form', array(
                    'siteId' => $siteId,
                ))
            )
        );

        $form->handleRequest($request);

        $this->handleForm($form, $this->get('translator')->trans('php_orchestra_backoffice.form.website.success'));

        $this->dispatchEvent(SiteEvents::SITE_UPDATE, new SiteEvent($site));

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/site/new", name="php_orchestra_backoffice_site_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $siteClass = $this->container->getParameter('php_orchestra_model.document.site.class');
        $site = new $siteClass();
        $form = $this->createForm(
            'site',
            $site,
            array(
                'action' => $this->generateUrl('php_orchestra_backoffice_site_new'),
                'method' => 'POST',
            )
        );

        $form->handleRequest($request);

        $this->handleForm($form, $this->get('translator')->trans('php_orchestra_backoffice.form.website.creation'), $site);

        $statusCode = 200;
        if ($form->getErrors()->count() > 0) {
            $statusCode = 400;
        } elseif (!is_null($site->getSiteId())) {
            $url = $this->generateUrl('php_orchestra_backoffice_site_form', array('siteId' => $site->getSiteId()));

            $this->dispatchEvent(SiteEvents::SITE_CREATE, new SiteEvent($site));

            return $this->redirect($url);
        };

        $response = new Response('', $statusCode, array('Content-type' => 'text/html; charset=utf-8'));

        return $this->renderAdminForm($form, array(), $response);
    }
}
