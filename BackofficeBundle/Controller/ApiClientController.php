<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiClientController
 */
class ApiClientController extends AbstractAdminController
{
    /**
     * @param Request $request
     *
     * @Config\Route("/new", name="open_orchestra_backoffice_api_client_new")
     * @Config\Method({"GET","POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_API_CLIENT')")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $manager = $this->get('open_orchestra_user.domain_manager.api_client');
        $apiClient = $manager->create();

        $form = $this->createForm(
            'api_client',
            $apiClient,
            array('action' => $this->generateUrl('open_orchestra_backoffice_api_client_new'))
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $manager->save($apiClient);

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('open_orchestra_backoffice.form.api_client.new.success')
            );

            return $this->redirect($this->generateUrl('open_orchestra_backoffice_api_client_form', array(
                'apiClientId' => $apiClient->getId()
            )));
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $apiClientId
     *
     * @Config\Route("/form/{apiClientId}", name="open_orchestra_backoffice_api_client_form")
     * @Config\Method({"GET","POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_API_CLIENT')")
     *
     * @return Response
     */
    public function formAction(Request $request, $apiClientId)
    {
        $apiClient = $this->get('open_orchestra_user.repository.api_client')->find($apiClientId);

        $form = $this->createForm('api_client', $apiClient, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_api_client_form', array(
                'apiClientId' => $apiClientId,
            )))
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $manager = $this->get('open_orchestra_user.domain_manager.api_client');
            $manager->save($apiClient);

            $this->get('session')->getFlashBag()->add(
                'success', $this->get('translator')->trans('open_orchestra_backoffice.form.api_client.edit.success')
            );
        }

        return $this->renderAdminForm($form);
    }
}
