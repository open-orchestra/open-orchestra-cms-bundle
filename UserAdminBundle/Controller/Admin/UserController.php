<?php

namespace OpenOrchestra\UserAdminBundle\Controller\Admin;

use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\BaseApi\Exceptions\HttpException\UserNotFoundHttpException;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
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
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, UserInterface::ENTITY_TYPE);

        $userClass = $this->container->getParameter('open_orchestra_user.document.user.class');
        /** @var UserInterface $user */
        $user = new $userClass();
        $user->setEmail($request->get('email'));
        $user->setLastName($request->get('lastName'));
        $user->setFirstName($request->get('firstName'));

        $form = $this->createForm('oo_registration_user', $user, array(
                'action' => $this->generateUrl('open_orchestra_user_admin_new'),
                'required_password' => true,
                'validation_groups' => array('Registration'),
            )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('object_manager');
            $documentManager->persist($user);
            $documentManager->flush();
            $message = $this->get('translator')->trans('open_orchestra_user_admin.new.success');

            $this->dispatchEvent(UserEvents::USER_CREATE, new UserEvent($user));
            $response = new Response(
                $message,
                Response::HTTP_CREATED,
                array('Content-type' => 'text/plain; charset=utf-8', 'userId' => $user->getId())
            );

            return $response;
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
     * @throws UserNotFoundHttpException
     */
    public function formAction(Request $request, $userId)
    {
        $user = $this->get('open_orchestra_user.repository.user')->find($userId);

        if ($user instanceof UserInterface) {
            $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $user);
            $user = $this->refreshLanguagesByAliases($user);

            return $this->renderForm($request, $user, false);
        }

        throw new UserNotFoundHttpException();
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
            )
        );
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

        return $this->renderAdminForm($form);
    }

    /**
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    protected function refreshLanguagesByAliases(UserInterface $user)
    {
        $sites = array();
        $siteIds = array();
        $allSite = $this->get('security.authorization_checker')->isGranted(ContributionRoleInterface::PLATFORM_ADMIN);

        if ($allSite) {
            $sites = $this->container->get('open_orchestra_model.repository.site')->findByDeleted(false);
        } else {
            foreach ($user->getGroups() as $group) {
                /** @var SiteInterface $site */
                $site = $group->getSite();
                if (!$group->isDeleted() && !$site->isDeleted() && !in_array($site->getSiteId(), $siteIds)) {
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
