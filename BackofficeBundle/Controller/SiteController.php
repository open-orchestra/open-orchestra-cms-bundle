<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\Event\SiteEvent;
use OpenOrchestra\ModelInterface\SiteEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

/**
 * Class SiteController
 */
class SiteController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $siteId
     *
     * @Config\Route("/site/form/{siteId}", name="open_orchestra_backoffice_site_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $siteId)
    {
        $site = $this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $site);

        if ($site instanceof SiteInterface) {
            $oldAliases = $site->getAliases();
            $form = $this->createForm(
                'oo_site',
                $site,
                array(
                    'action' => $this->generateUrl('open_orchestra_backoffice_site_form', array(
                        'siteId' => $siteId,
                    )),
                    'delete_button' => $this->isGranted(ContributionActionInterface::DELETE, $site)
                )
            );

            $form->handleRequest($request);
            $message =  $this->get('translator')->trans('open_orchestra_backoffice.form.website.success');
            if ($this->handleForm($form, $message)) {
                $this->dispatchEvent(SiteEvents::SITE_UPDATE, new SiteEvent($site, $oldAliases));
            }

            return $this->renderAdminForm($form);
        }

        return new Response();
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/site/new", name="open_orchestra_backoffice_site_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $site = $this->get('open_orchestra_backoffice.manager.site')->initializeNewSite();
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, $site);

        $form = $this->createForm('oo_site', $site, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_site_new'),
            'method' => 'POST',
            'new_button' => true
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('object_manager');
            $documentManager->persist($site);
            $documentManager->flush();
            $message = $this->get('translator')->trans('open_orchestra_backoffice.form.website.creation');
            $this->get('session')->getFlashBag()->add('success', $message);

            $this->dispatchEvent(SiteEvents::SITE_CREATE, new SiteEvent($site, null));
            $response = new Response(
                '',
                Response::HTTP_CREATED,
                array('Content-type' => 'text/plain; charset=utf-8', 'siteId' => $site->getSiteId(), 'name' => $site->getName())
            );

            return $response;
        }

        return $this->renderAdminForm($form);
    }
}
