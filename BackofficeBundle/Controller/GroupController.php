<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\UserBundle\Event\GroupEvent;
use OpenOrchestra\UserBundle\GroupEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GroupController
 *
 * @Config\Route("group")
 */
class GroupController extends AbstractAdminController
{
    /**
     * @param Request $request
     *
     * @Config\Route("/new", name="open_orchestra_backoffice_group_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_CREATE_GROUP')")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $groupClass = $this->container->getParameter('open_orchestra_user.document.group.class');
        /** @var GroupInterface $group */
        $group = new $groupClass();

        $form = $this->createForm('oo_group', $group, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_group_new'),
            'method' => 'POST',
        ));

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.group.new.success');

        if ($this->handleForm($form, $message, $group)) {
            $this->dispatchEvent(GroupEvents::GROUP_CREATE, new GroupEvent($group));
            $response = new Response('', Response::HTTP_CREATED, array('Content-type' => 'text/html; charset=utf-8'));

            return $this->render('BraincraftedBootstrapBundle::flash.html.twig', array(), $response);
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $groupId
     *
     * @Config\Route("/form/{groupId}", name="open_orchestra_backoffice_group_form")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("is_granted('ROLE_ACCESS_UPDATE_GROUP')")
     *
     * @return Response
     */
    public function formAction(Request $request, $groupId)
    {
        $group = $this->get('open_orchestra_user.repository.group')->find($groupId);

        $form = $this->createForm('oo_group', $group, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_group_form', array(
                'groupId' => $groupId,
            )))
        );

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_backoffice.form.group.edit.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(GroupEvents::GROUP_UPDATE, new GroupEvent($group));
        }
        $title = $this->get('translator')->trans('open_orchestra_backoffice.form.group.title');

        return $this->renderAdminForm($form, array('title' => $title));
    }
}
