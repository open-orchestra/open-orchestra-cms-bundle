<?php

namespace PHPOrchestra\UserBundle\Controller;

use PHPOrchestra\UserBundle\Document\Role;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RoleController
 *
 * @Config\Route("role")
 */
class RoleController extends Controller
{
    /**
     * @param Request $request
     *
     * @Config\Route("/new", name="php_orchestra_user_role_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $role = new Role();

        $form = $this->createForm(
            'role',
            $role,
            array(
                'action' => $this->generateUrl('php_orchestra_user_role_new')
            )
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($role);
            $documentManager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('php_orchestra_user.form.role.new.success')
            );
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param string  $roleId
     *
     * @Config\Route("/form/{roleId}", name="php_orchestra_user_role_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $roleId)
    {
        $role = $this->get('php_orchestra_user.repository.role')->find($roleId);

        $form = $this->createForm(
            'role',
            $role,
            array(
                'action' => $this->generateUrl('php_orchestra_user_role_form', array(
                    'roleId' => $roleId,
                ))
            )
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($role);
            $documentManager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('php_orchestra_user.form.role.edit.success')
            );
        }

        return $this->render('PHPOrchestraBackofficeBundle:Editorial:template.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
