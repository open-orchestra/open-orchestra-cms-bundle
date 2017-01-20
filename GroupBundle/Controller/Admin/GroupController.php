<?php

namespace OpenOrchestra\GroupBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\Backoffice\Model\GroupInterface;
use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\UserBundle\Event\GroupEvent;
use OpenOrchestra\UserBundle\GroupEvents;

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
     * @Config\Route("/new", name="open_orchestra_group_new")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $groupClass = $this->container->getParameter('open_orchestra_user.document.group.class');
        /** @var GroupInterface $group */
        $group = new $groupClass();
        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, $group);

        $form = $this->createForm('oo_group', $group, array(
            'action' => $this->generateUrl('open_orchestra_group_new'),
            'method' => 'POST',
            'new_button' => true
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('object_manager');
            $documentManager->persist($group);
            $documentManager->flush();
            $message = $this->get('translator')->trans('open_orchestra_group.form.group.new.success');

            $this->dispatchEvent(GroupEvents::GROUP_CREATE, new GroupEvent($group));
            $response = new Response(
                $message,
                Response::HTTP_CREATED,
                array('Content-type' => 'text/plain; charset=utf-8', 'groupId' => $group->getId(), 'name' => $group->getName())
            );

            return $response;
        }

        return $this->renderAdminForm($form);
    }

    /**
     * @param Request $request
     * @param string  $groupId
     *
     * @Config\Route("/form/{groupId}", name="open_orchestra_group_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request, $groupId)
    {
        $group = $this->get('open_orchestra_user.repository.group')->find($groupId);
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, $group);

        $form = $this->createForm('oo_group', $group, array(
            'action' => $this->generateUrl('open_orchestra_group_form', array(
                'groupId' => $groupId,
            )),
            'delete_button' => (0 === $this->get('open_orchestra_user.repository.user')->getCountsUsersByGroups(array($groupId)))
        ));

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_group.form.group.edit.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(GroupEvents::GROUP_UPDATE, new GroupEvent($group));
        }
        $title = $this->get('translator')->trans('open_orchestra_group.form.group.title');

        return $this->renderAdminForm($form, array('title' => $title));
    }
}
