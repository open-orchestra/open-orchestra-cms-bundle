<?php

namespace OpenOrchestra\UserAdminBundle\Controller\Admin;

use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\UserBundle\Event\UserEvent;
use OpenOrchestra\UserBundle\UserEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\UserBundle\Model\UserInterface;
use OpenOrchestra\Backoffice\Security\ContributionRoleInterface;

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
     * @return Response
     */
    public function newAction(Request $request)
    {
        $userClass = $this->container->getParameter('open_orchestra_user.document.user.class');
        /** @var UserInterface $user */
        $user = new $userClass();
        $user = $this->refreshLanguagesByAliases($user);
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, $user);

        $form = $this->createForm('oo_registration_user', $user, array(
            'action' => $this->generateUrl('open_orchestra_user_admin_new'),
            'validation_groups' => array('Registration')
        ));
        $form->handleRequest($request);
        if ($this->handleForm($form, $this->get('translator')->trans('open_orchestra_user_admin.new.success'), $user)) {
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
     * @return Response
     */
    public function formAction(Request $request, $userId)
    {
        $user = $this->get('open_orchestra_user.repository.user')->find($userId);

        if ($user instanceof UserInterface) {
            $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $user);
            $user = $this->refreshLanguagesByAliases($user);

            return $this->renderForm($request, $user, false);
        }

        return new Response();
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
        $user = $this->refreshLanguagesByAliases($this->getUser());

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
            'edit_groups' => $editGroups,
            'self_editing' => $selfEdit,
            'validation_groups' => array('Profile', 'UpdatePassword', 'Default'),
            'current_user' => $this->getUser(),
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ('' != $user->getPlainPassword()) {
                $userManager = $this->get('fos_user.user_manager');
                $userManager->updatePassword($user);
                $this->dispatchEvent(UserEvents::USER_CHANGE_PASSWORD, new UserEvent($user));
            }
            $this->handleForm($form, $this->get('translator')->trans('open_orchestra_user_admin.update.success'));
            $this->dispatchEvent(UserEvents::USER_UPDATE, new UserEvent($user));
        }

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
     * @return Response
     */
    public function changePasswordAction(Request $request, $userId)
    {
        /* @var UserInterface $user */
        $user = $this->get('open_orchestra_user.repository.user')->find($userId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $user);

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
     *
     * @return UserInterface
     */
    protected function refreshLanguagesByAliases(UserInterface $user)
    {
        $sites = array();
        $siteIds = array();
        $allSite = $this->getUser()->hasRole(ContributionRoleInterface::PLATFORM_ADMIN) || $this->getUser()->hasRole(ContributionRoleInterface::DEVELOPER);

        if ($allSite) {
            $sites = $this->container->get('open_orchestra_model.repository.site')->findByDeleted(false);
        } else {
            foreach ($user->getGroups() as $group) {
                /** @var SiteInterface $site */
                $site = $group->getSite();
                if (!$site->isDeleted() && !in_array($site->getSiteId(), $siteIds)) {
                    $siteIds[] = $site->getSiteId();
                    $sites[] = $site;
                }
            }
        }

        foreach ($sites as $site) {
            if(!$user->hasLanguageBySite($site->getSiteId())) {
                $user->setLanguageBySite($site->getSiteId(), $site->getDefaultLanguage());
            }
        }

        return $user;
    }

}
