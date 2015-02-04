<?php

namespace PHPOrchestra\BackofficeBundle\Controller;

use PHPOrchestra\ModelInterface\Event\RoleEvent;
use PHPOrchestra\ModelInterface\Model\RoleInterface;
use PHPOrchestra\ModelInterface\RoleEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RoleController
 *
 * @Config\Route("role")
 */
class RoleController extends AbstractAdminController
{
    /**
     * @param Request $request
     *
     * @Config\Route("/new", name="php_orchestra_backoffice_role_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $roleClass = $this->container->getParameter('php_orchestra_model.document.role.class');
        /** @var RoleInterface $role */
        $role = new $roleClass();

        $form = $this->createForm('role', $role, array(
            'action' => $this->generateUrl('php_orchestra_backoffice_role_new')
        ));

        $form->handleRequest($request);
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($role);
            $documentManager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('php_orchestra_backoffice.form.role.new.success')
            );

            $this->dispatchEvent(RoleEvents::ROLE_CREATE, new RoleEvent($role));

            return $this->redirect($this->generateUrl('php_orchestra_backoffice_role_form', array(
                'roleId' => $role->getId()
            )));
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $roleId
     *
     * @Config\Route("/form/{roleId}", name="php_orchestra_backoffice_role_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $roleId)
    {
        $role = $this->get('php_orchestra_model.repository.role')->find($roleId);

        $form = $this->createForm('role', $role, array(
            'action' => $this->generateUrl('php_orchestra_backoffice_role_form', array(
                'roleId' => $roleId,
            )))
        );

        $form->handleRequest($request);
        $this->handleForm($form, $this->get('translator')->trans('php_orchestra_backoffice.form.role.edit.success'), $role);

        $this->dispatchEvent(RoleEvents::ROLE_UPDATE, new RoleEvent($role));

        return $this->renderAdminForm($form);
    }
}
