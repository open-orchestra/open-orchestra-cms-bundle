<?php

namespace OpenOrchestra\WorkflowAdminBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\ModelBundle\Document\WorkflowProfile;
use OpenOrchestra\ModelInterface\WorkflowProfileEvents;
use OpenOrchestra\ModelInterface\Event\WorkflowProfileEvent;

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
        $currentLocale = $this->get('open_orchestra_backoffice.context_backoffice_manager')->getBackOfficeLanguage();
        $profiles = $this->get('open_orchestra_model.repository.workflow_profile')
            ->findAllOrderedByLocale($currentLocale);

        $form = $this->createForm('oo_workflow_transitions', $profiles, array(
           'action' => $this->generateUrl('open_orchestra_workflow_admin_transitions_form')
        ));

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_workflow_admin.form.transitions.success');
        if ($this->handleForm($form, $message)) {
            foreach ($profiles as $profile) {
                $this->dispatchEvent(WorkflowProfileEvents::WORKFLOW_PROFILE_UPDATE, new WorkflowProfileEvent($profile));
            }
        }

        return $this->renderAdminForm($form);
    }
}
