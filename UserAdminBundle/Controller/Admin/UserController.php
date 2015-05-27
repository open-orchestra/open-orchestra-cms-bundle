<?php

namespace OpenOrchestra\UserAdminBundle\Controller\Admin;

use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\UserBundle\Event\UserEvent;
use OpenOrchestra\UserBundle\UserEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Doctrine\UserManager;
use OpenOrchestra\UserBundle\Document\User;
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
     * @Config\Security("has_role('ROLE_ACCESS_USER')")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $userClass = $this->container->getParameter('open_orchestra_user.document.user.class');
        /** @var UserInterface $user */
        $user = new $userClass();
        $form = $this->createForm('registration_user', $user, array(
            'action' => $this->generateUrl('open_orchestra_user_admin_new'),
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
     * @Config\Security("has_role('ROLE_ACCESS_USER')")
     *
     * @return Response
     */
    public function formAction(Request $request, $userId)
    {
        $user = $this->get('open_orchestra_user.repository.user')->find($userId);
        $form = $this->createForm('user', $user, array(
            'action' => $this->generateUrl('open_orchestra_user_admin_user_form', array('userId' => $userId))
        ));
        $form->handleRequest($request);
        $this->handleForm($form, $this->get('translator')->trans('open_orchestra_user.update.success'));
        $this->dispatchEvent(UserEvents::USER_UPDATE, new UserEvent($user));

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string $userId
     *
     * @Config\Route("/password/change/{userId}", name="open_orchestra_user_admin_user_change_password")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_USER')")
     *
     * @return Response
     */

    public function changePasswordAction(Request $request, $userId)
    {
        /* @var User $user */
        $user = $this->get('open_orchestra_user.repository.user')->find($userId);
        $form = $this->createForm('user_change_password', $user, array(
            'action' => $this->generateUrl('open_orchestra_user_admin_user_change_password', array('userId' => $userId))
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updatePassword($user);
            $this->handleForm($form, $this->get('translator')->trans('open_orchestra_user.update.success'));
            $this->dispatchEvent(UserEvents::USER_CHANGE_PASSWORD, new UserEvent($user));
        }



        return $this->renderAdminForm($form);
    }

}
