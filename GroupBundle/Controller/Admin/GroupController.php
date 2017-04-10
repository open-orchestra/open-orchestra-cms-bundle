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
        $groupClass = $this->getParameter('open_orchestra_user.document.group.class');
        /** @var GroupInterface $group */
        $group = new $groupClass();

        $siteId = $this->get('open_orchestra_backoffice.context_manager')->getCurrentSiteId();
        $group->setSite($this->get('open_orchestra_model.repository.site')->findOneBySiteId($siteId));

        $this->denyAccessUnlessGranted(ContributionActionInterface::CREATE, $group);

        $form = $this->createForm('oo_group', $group, array(
            'action'     => $this->generateUrl('open_orchestra_group_new'),
            'creation'   => true,
            'method'     => 'POST'
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $documentManager = $this->get('object_manager');
            $documentManager->persist($group);
            $documentManager->flush();
            $message = $this->get('translator')->trans('open_orchestra_group.form.group.new.success');
            $this->get('session')->getFlashBag()->add('success', $message);

            $this->dispatchEvent(GroupEvents::GROUP_CREATE, new GroupEvent($group));
            $response = new Response(
                '',
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
            'delete_button' => ($this->isGranted(ContributionActionInterface::DELETE, $group) && $this->get('open_orchestra_backoffice.business_rules_manager')->isGranted(ContributionActionInterface::DELETE, $group))
        ));

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_group.form.group.edit.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(GroupEvents::GROUP_UPDATE, new GroupEvent($group));
        }

        return $this->renderAdminForm($form);
    }
}
