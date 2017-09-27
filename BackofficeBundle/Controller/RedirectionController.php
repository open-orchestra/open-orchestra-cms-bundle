<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\Event\RedirectionEvent;
use OpenOrchestra\ModelInterface\Model\RedirectionInterface;
use OpenOrchestra\ModelInterface\RedirectionEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;

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
     * @Config\Method({"GET", "POST", "PATCH"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $redirectionClass = $this->getParameter('open_orchestra_model.document.redirection.class');
        /** @var RedirectionInterface $redirection */
        $redirection = new $redirectionClass();
        $redirection->setSiteId($this->get('open_orchestra_backoffice.context_backoffice_manager')->getSiteId());
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, $redirection);
        $action = $this->generateUrl('open_orchestra_backoffice_redirection_new');
        $form = $this->createRedirectionForm($request, array(
            'action' => $action,
            'new_button' => true
        ), $redirection);
        $form->handleRequest($request);

        if ('PATCH' !== $request->getMethod()) {
            $message = $this->get('translator')->trans('open_orchestra_backoffice.form.redirection.new.success');
            if ($this->handleForm($form, $message, $redirection)) {
                $this->dispatchEvent(RedirectionEvents::REDIRECTION_CREATE, new RedirectionEvent($redirection));
                $response = new Response(
                    '',
                    Response::HTTP_CREATED,
                    array(
                        'Content-type' => 'text/html; charset=utf-8',
                        'redirectionId' => $redirection->getId(),
                    )
                );

                return $response;
            }
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $redirectionId
     *
     * @Config\Route("/form/{redirectionId}", name="open_orchestra_backoffice_redirection_form")
     * @Config\Method({"GET", "POST", "PATCH"})
     *
     * @return Response
     */
    public function formAction(Request $request, $redirectionId)
    {
        $redirection = $this->get('open_orchestra_model.repository.redirection')->find($redirectionId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $redirection);

        $action = $this->generateUrl('open_orchestra_backoffice_redirection_form', array('redirectionId' => $redirectionId));
        $form = $this->createRedirectionForm($request, array('action' => $action), $redirection);

        $form->handleRequest($request);
        if ('PATCH' !== $request->getMethod()) {
            $message = $this->get('translator')->trans('open_orchestra_backoffice.form.redirection.edit.success');
            if ($this->handleForm($form, $message)) {
                $this->dispatchEvent(RedirectionEvents::REDIRECTION_UPDATE, new RedirectionEvent($redirection));
            }
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request              $request
     * @param string               $option
     * @param ContentTypeInterface $contentType
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createRedirectionForm(Request $request, $option, RedirectionInterface $redirection)
    {
        $method = "POST";
        if ("PATCH" === $request->getMethod()) {
            $option["validation_groups"] = false;
            $method = "PATCH";
        }
        $option["method"] = $method;

        return $this->createForm('oo_redirection', $redirection, $option);
    }
}
