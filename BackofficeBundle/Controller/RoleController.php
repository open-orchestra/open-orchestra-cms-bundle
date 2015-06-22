<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\ModelInterface\Event\RoleEvent;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\ModelInterface\RoleEvents;
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
     * @Config\Route("/new", name="open_orchestra_backoffice_role_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_ROLE')")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $roleClass = $this->container->getParameter('open_orchestra_model.document.role.class');
        /** @var RoleInterface $role */
        $role = new $roleClass();

        $form = $this->createForm('role', $role, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_role_new')
        ));

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.role.new.success');
        if ($this->handleForm($form, $message, $role)) {
            $this->dispatchEvent(RoleEvents::ROLE_CREATE, new RoleEvent($role));

            return $this->redirect($this->generateUrl('open_orchestra_backoffice_role_form', array(
                'roleId' => $role->getId()
            )));
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $roleId
     *
     * @Config\Route("/form/{roleId}", name="open_orchestra_backoffice_role_form")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_ROLE')")
     *
     * @return Response
     */
    public function formAction(Request $request, $roleId)
    {
        $role = $this->get('open_orchestra_model.repository.role')->find($roleId);

        $form = $this->createForm('role', $role, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_role_form', array(
                'roleId' => $roleId,
            )))
        );

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.role.edit.success');

        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(RoleEvents::ROLE_UPDATE, new RoleEvent($role));
        }

        return $this->renderAdminForm($form);
    }
}
