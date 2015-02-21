<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\Event\RedirectionEvent;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;
use OpenOrchestra\ModelInterface\RedirectionEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RedirectionController
 *
 * @Config\Route("redirection")
 */
class RedirectionController extends AbstractAdminController
{
    /**
     * @param Request $request
     *
     * @Config\Route("/new", name="open_orchestra_backoffice_redirection_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $redirectionClass = $this->container->getParameter('open_orchestra_model.document.redirection.class');
        /** @var RedirectionInterface $redirection */
        $redirection = new $redirectionClass();

        $form = $this->createForm('redirection', $redirection, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_redirection_new')
        ));

        $form->handleRequest($request);
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($redirection);
            $documentManager->flush();

            $this->dispatchEvent(RedirectionEvents::REDIRECTION_CREATE, new RedirectionEvent($redirection));

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('open_orchestra_backoffice.form.redirection.new.success')
            );


            return $this->redirect($this->generateUrl('open_orchestra_backoffice_redirection_form', array(
                'redirectionId' => $redirection->getId()
            )));
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $redirectionId
     *
     * @Config\Route("/form/{redirectionId}", name="open_orchestra_backoffice_redirection_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $redirectionId)
    {
        $redirection = $this->get('open_orchestra_model.repository.redirection')->find($redirectionId);

        $form = $this->createForm('redirection', $redirection, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_redirection_form', array(
                'redirectionId' => $redirectionId,
            )))
        );

        $form->handleRequest($request);
        $this->handleForm($form, $this->get('translator')->trans('open_orchestra_backoffice.form.redirection.edit.success'), $redirection);

        $this->dispatchEvent(RedirectionEvents::REDIRECTION_UPDATE, new RedirectionEvent($redirection));

        return $this->renderAdminForm($form);
    }
}
