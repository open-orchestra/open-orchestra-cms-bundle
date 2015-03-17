<?php

namespace OpenOrchestra\BackofficeBundle\Controller;

use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
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
     * @return Response
     */
    public function newAction(Request $request)
    {
        $groupClass = $this->container->getParameter('open_orchestra_user.document.group.class');
        /** @var GroupInterface $group */
        $group = new $groupClass();

        $form = $this->createForm('group', $group, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_group_new')
        ));

        $form->handleRequest($request);
        if ($form->isValid()) {
            $documentManager = $this->get('doctrine.odm.mongodb.document_manager');
            $documentManager->persist($group);
            $documentManager->flush();

            $this->dispatchEvent(GroupEvents::GROUP_CREATE, new GroupEvent($group));

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('open_orchestra_backoffice.form.group.new.success')
            );


            return $this->redirect($this->generateUrl('open_orchestra_backoffice_group_form', array(
                'groupId' => $group->getId()
            )));
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
     * @return Response
     */
    public function formAction(Request $request, $groupId)
    {
        $group = $this->get('open_orchestra_user.repository.group')->find($groupId);

        $form = $this->createForm('group', $group, array(
            'action' => $this->generateUrl('open_orchestra_backoffice_group_form', array(
                'groupId' => $groupId,
            )))
        );

        $form->handleRequest($request);
        $this->handleForm($form, $this->get('translator')->trans('open_orchestra_backoffice.form.group.edit.success'), $group);

        $this->dispatchEvent(GroupEvents::GROUP_UPDATE, new GroupEvent($group));

        return $this->renderAdminForm($form);
    }
}
