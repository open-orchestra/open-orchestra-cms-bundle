<?php

namespace OpenOrchestra\WorkflowAdminBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\ModelBundle\Document\WorkflowProfile;

/**
 * Class TransitionController
 */
class TransitionController extends AbstractAdminController
{
    /**
     * @param Request $request
     *
     * @Config\Route("/transition/form", name="open_orchestra_workflow_admin_transitions_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, WorkflowProfile::ENTITY_TYPE);
        $data = array(
            'profiles' => $this->get('open_orchestra_model.repository.workflow_profile')->findAll(),
            'labels'   => array('draft', 'published', 'to translate', 'pending')
        );

        $form = $this->createForm('oo_workflow_transitions', $data, array(
           'action' => $this->generateUrl('open_orchestra_workflow_admin_transitions_form')
        ));

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_workflow_admin.form.transitions.success');
        if ($this->handleForm($form, $message)) {
            $this->dispatchEvent(StatusEvents::STATUS_UPDATE, new StatusEvent($status));
        }

        return $this->renderAdminForm($form);
    }
}
