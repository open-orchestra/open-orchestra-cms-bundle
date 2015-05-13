<?php

namespace OpenOrchestra\WorkflowFonctionAdminBundle\Controller\Admin;

use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\WorkflowFonction\Event\WorkflowFonctionEvent;
use OpenOrchestra\WorkflowFonction\WorkflowFonctionEvents;
use OpenOrchestra\WorkflowFonction\Model\WorkflowFonctionInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WorkflowFonctionController
 *
 * @Config\Route("workflowfonction")
 */
class WorkflowFonctionController extends AbstractAdminController
{
    /**
     * @param Request $request
     * @param string  $workflowFonctionId
     *
     * @Config\Route("/form/{workflowFonctionId}", name="open_orchestra_backoffice_workflowfonction_form")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_WORKFLOWFONCTION')")
     *
     * @return Response
     */
    public function formAction(Request $request, $workflowFonctionId)
    {
        $workflowFonctionRepository = $this->container->get('open_orchestra_workflowfonction.repository.workflowFonction');
        $workflowFonction = $workflowFonctionRepository->find($workflowFonctionId);

        $url = $this->generateUrl('open_orchestra_backoffice_workflowfonction_form', array('workflowFonctionId' => $workflowFonctionId));

        return $this->generateForm($request, $workflowFonction, $url, WorkflowFonctionEvents::WORKFLOWFONCTION_UPDATE);
    }

    /**
     * @param Request $request
     *
     * @Config\Route("/new", name="open_orchestra_backoffice_workflowfonction_new")
     * @Config\Method({"GET", "POST"})
     *
     * @Config\Security("has_role('ROLE_ACCESS_WORKFLOWFONCTION')")
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $workflowFonctionClass = $this->container->getParameter('open_orchestra_workflowfonction.document.workflowFonction.class');
        $workflowFonction = new $workflowFonctionClass();

        $url = $this->generateUrl('open_orchestra_backoffice_workflowfonction_new');

        return $this->generateForm($request, $workflowFonction, $url, WorkflowFonctionEvents::WORKFLOWFONCTION_CREATE);
    }

    /**
     * @param Request                   $request
     * @param WorkflowFonctionInterface $workflowFonction
     * @param string                    $url
     * @param string                    $workflowFonctionEvents
     *
     * @return Response
     */
    protected function generateForm(Request $request, WorkflowFonctionInterface $workflowFonction, $url, $workflowFonctionEvents)
    {
        $form = $this->createForm('workflowfonction', $workflowFonction, array('action' => $url));
        $form->handleRequest($request);
        $this->handleForm(
            $form,
            $this->get('translator')->trans('open_orchestra_workflowfonction.form.workflowfonction.success'),
            $workflowFonction
        );

        $this->dispatchEvent($workflowFonctionEvents, new WorkflowFonctionEvent($workflowFonction));

        return $this->renderAdminForm($form);
    }
}
