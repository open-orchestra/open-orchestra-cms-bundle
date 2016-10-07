<?php

namespace OpenOrchestra\UserAdminBundle\Controller\Admin;

use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\UserBundle\Event\UserEvent;
use OpenOrchestra\UserBundle\UserEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserController
 *
 * @Config\Route("user")
 */
class UserController extends AbstractAdminController
{
    /**
     * @param Request $request
     *
     * @Config\Route("/new", name="open_orchestra_user_admin_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_CREATE_USER')")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $userClass = $this->container->getParameter('open_orchestra_user.document.user.class');
        /** @var UserInterface $user */
        $user = new $userClass();
        $form = $this->createForm('oo_registration_user', $user, array(
            'action' => $this->generateUrl('open_orchestra_user_admin_new'),
            'validation_groups' => array('Registration')
        ));
        $form->handleRequest($request);
        if ($this->handleForm($form, $this->get('translator')->trans('open_orchestra_user.new.success'), $user)) {
            $url = $this->generateUrl('open_orchestra_user_admin_user_form', array('userId' => $user->getId()));

            $this->dispatchEvent(UserEvents::USER_CREATE, new UserEvent($user));

            return $this->redirect($url);
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $userId
     *
     * @Config\Route("/form/{userId}", name="open_orchestra_user_admin_user_form")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_USER')")
     *
     * @return Response
     */
    public function formAction(Request $request, $userId)
    {
        $user = $this->get('open_orchestra_user.repository.user')->find($userId);

        return $this->renderForm($request, $user, false);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/form", name="open_orchestra_user_admin_user_self_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formSelfAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->renderForm($request, $user, true);
    }

    /**
     * @param Request       $request
     * @param UserInterface $user
     * @param boolean       $selfEdit
     *
     * @return Response
     */
    protected function renderForm(Request $request, UserInterface $user, $selfEdit)
    {
        $action = $this->generateUrl('open_orchestra_user_admin_user_form', array('userId' => $user->getId()));
        $editGroups = true;

        if ($selfEdit) {
            $action = $this->generateUrl('open_orchestra_user_admin_user_self_form');
            $editGroups = false;
        }

        $form = $this->createForm('oo_user', $user, array(
            'action' => $action,
            'validation_groups' => array('Profile'),
            'edit_groups' => $editGroups
        ));
        $form->handleRequest($request);
        $this->handleForm($form, $this->get('translator')->trans('open_orchestra_user.update.success'));
        $this->dispatchEvent(UserEvents::USER_UPDATE, new UserEvent($user));

        $title = 'open_orchestra_user_admin.form.title';
        $title = $this->get('translator')->trans($title);

        return $this->renderAdminForm($form, array('title' => $title));
    }

    /**
     * @param Request $request
     * @param string  $userId
     *
     * @Config\Route("/password/change/{userId}", name="open_orchestra_user_admin_user_change_password")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_USER')")
     *
     * @return Response
     */
    public function changePasswordAction(Request $request, $userId)
    {
        /* @var UserInterface $user */
        $user = $this->get('open_orchestra_user.repository.user')->find($userId);
        $url = 'open_orchestra_user_admin_user_change_password';

        return $this->renderChangePassword($request, $user, $url);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/password/change", name="open_orchestra_user_admin_user_self_change_password")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function selfChangePasswordAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $url = "open_orchestra_user_admin_user_self_change_password";

        return $this->renderChangePassword($request, $user, $url);
    }

    /**
     * @param Request       $request
     * @param UserInterface $user
     * @param string        $url
     *
     * @return Response
     */
    protected function renderChangePassword(Request $request, UserInterface $user, $url)
    {
        $form = $this->createForm('oo_user_change_password', $user, array(
            'action' => $this->generateUrl($url, array('userId' => $user->getId())),
            'validation_groups' => array('UpdatePassword', 'Default'),
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updatePassword($user);
            $this->handleForm($form, $this->get('translator')->trans('open_orchestra_user.update.success'));
            $this->dispatchEvent(UserEvents::USER_CHANGE_PASSWORD, new UserEvent($user));
        }

        $title = 'open_orchestra_user_admin.password.title';
        $title = $this->get('translator')->trans($title);

        return $this->renderAdminForm($form, array('title' => $title));
    }
}
