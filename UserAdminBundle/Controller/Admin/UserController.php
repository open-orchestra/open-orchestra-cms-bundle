<?php

namespace OpenOrchestra\UserAdminBundle\Controller\Admin;

use OpenOrchestra\UserBundle\Event\UserEvent;
use OpenOrchestra\UserBundle\Document\User;
use OpenOrchestra\UserBundle\UserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;

/**
 * Class UserController
 *
 * @Config\Route("user")
 */
class UserController extends Controller
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
        $user = new User();
        $url = $this->generateUrl('open_orchestra_user_admin_new');
        $event = UserEvents::USER_CREATE;
        $form = $this->handleForm($request, 'registration_user', $user, $url, $event);
        if ($form->isValid()) {
            $url = $this->generateUrl('open_orchestra_user_admin_user_form', array('userId' => $user->getId()));

            return $this->redirect($url);
        }

        return $this->renderForm($form);
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
        $url = $this->generateUrl('open_orchestra_user_admin_user_form', array('userId' => $userId));
        $event = UserEvents::USER_UPDATE;
        $form = $this->handleForm($request, 'user', $user, $url, $event);

        return $this->renderForm($form);
    }

    /**
     * @param Request $request
     * @param string  $formName
     * @param User    $user
     * @param string  $url
     * @param string  $event
     *
     * @return Form
     */
    protected function handleForm(Request $request, $formName, User $user, $url, $event)
    {
        $form = $this->createForm($formName, $user, array(
            'action' => $url
        ));

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->saveUser($user);
            $this->dispatchEvent($event, new UserEvent($user, $request));
        }

        return $form;
    }

    /**
     * @param Form $form
     *
     * @return Response
     */
    protected function renderForm(Form $form)
    {
        return $this->render($this->container->getParameter('open_orchestra_user.form_template'), array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param $user
     */
    protected function saveUser(User $user)
    {
        $type = $user->getId() ? 'update' : 'new';

        $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
        $documentManager->persist($user);
        $documentManager->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            $this->get('translator')->trans('open_orchestra_user.'.$type.'.success')
        );
    }

    /**
     * @param string $eventName
     * @param Event  $event
     */
    protected function dispatchEvent($eventName, $event)
    {
        $this->get('event_dispatcher')->dispatch($eventName, $event);
    }
}
