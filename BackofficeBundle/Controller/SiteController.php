<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\ModelBundle\Document\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SiteController
 */
class SiteController extends Controller
{
    /**
     * @param Request $request
     * @param int     $siteId
     *
     * @Config\Route("/site/form/{siteId}", name="php_orchestra_backoffice_site_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $siteId)
    {
        $site = $this->get('php_orchestra_model.repository.site')->findOneBy(array('siteId' => (int) $siteId));

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
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($site);
            $documentManager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('php_orchestra_backoffice.form.website.success')
            );
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
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
            )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($site);
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
