<?php

namespace OpenOrchestra\WorkflowAdminBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\Backoffice\Security\ContributionActionInterface;
use OpenOrchestra\BackofficeBundle\Controller\AbstractAdminController;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Event\StatusEvent;
use OpenOrchestra\ModelInterface\StatusEvents;

/**
 * Class ParametersController
 */
class ParametersController extends AbstractAdminController
{
    /**
     * @param Request $request
     *
     * @Config\Route("/workflow-parameters/form", name="open_orchestra_workflow_admin_parameters_form")
     * @Config\Method({"GET", "POST"})
     *
     * @return Response
     */
    public function formAction(Request $request)
    {
        $this->denyAccessUnlessGranted(ContributionActionInterface::EDIT, StatusInterface::ENTITY_TYPE);

        $statusCollection = $this->get('open_orchestra_model.repository.status')->findNotOutOfWorkflow();
        $data = array(
            'statuses' => $statusCollection,
            'labels'   => array(
                $this->get('translator')
                    ->trans('open_orchestra_workflow_admin.status.initial_state'          , array(), 'interface'),
                $this->get('translator')
                    ->trans('open_orchestra_workflow_admin.status.published_state'        , array(), 'interface'),
                $this->get('translator')
                    ->trans('open_orchestra_workflow_admin.status.auto_publish_from_state', array(), 'interface'),
                $this->get('translator')
                    ->trans('open_orchestra_workflow_admin.status.auto_unpublish_to_state', array(), 'interface'),
                $this->get('translator')
                    ->trans('open_orchestra_workflow_admin.status.translation_state'      , array(), 'interface')
            )
        );

        $form = $this->createForm('oo_workflow_parameters', $data, array(
            'action' => $this->generateUrl('open_orchestra_workflow_admin_parameters_form')
        ));

        $updatedStatusIndexes = array();
        if ($request->isMethod('POST')) {
            $updatedStatusIndexes = $this->getStatusIndexesToUpdate(
                $statusCollection,
                $request->get('oo_workflow_parameters')
            );
        }

        $form->handleRequest($request);
        $message = $this->get('translator')->trans('open_orchestra_workflow_admin.form.parameters.success');
        if ($this->handleForm($form, $message)) {
            foreach ($updatedStatusIndexes as $statusIndex) {
                $this->dispatchEvent(
                    StatusEvents::STATUS_UPDATE,
                    new StatusEvent($statusCollection[$statusIndex])
                );
            }
        }

        return $this->renderAdminForm($form);
    }

    /**
     * Get the list of status to update in $statusCollection
     * Only indexes in $statusCollection are returned
     *
     * @param array $statusCollection
     * @param array $formData
     *
     * @return array
     */
    protected function getStatusIndexesToUpdate(array $statusCollection, array $formData)
    {
        $statusMap = array();
        $updatedStatusIndexes = array();
        $formStatuses = $formData['statuses'];

        foreach ($statusCollection as $index => $status) {
            $statusMap[$index] = array(
                'initialState'         => $status->isInitialState(),
                'publishedState'       => $status->isPublishedState(),
                'autoPublishFromState' => $status->isAutoPublishFromState(),
                'autoUnpublishToState' => $status->isAutoUnpublishToState(),
                'translationState'     => $status->isTranslationState()
            );
        }

        foreach ($statusMap as $statusIndex => $oldParameters) {
            foreach ($oldParameters as $parameter => $oldValue) {
                if ($this->isParameterTurnedOn($parameter, $oldValue, $formStatuses, $statusIndex)
                    || $this->isParameterTurnedOff($parameter, $oldValue, $formStatuses, $statusIndex)
                ) {
                    $updatedStatusIndexes[] = $statusIndex;
                }
            }
        }

        return $updatedStatusIndexes;
    }

    /**
     * Check if $parameter is requested to be turned on
     *
     * @param string  $parameter
     * @param boolean $oldValue
     * @param array   $formStatuses
     * @param string  $statusIndex
     *
     * @return boolean
     */
    protected function isParameterTurnedOn($parameter, $oldValue, array $formStatuses, $statusIndex)
    {
        return (!$oldValue && isset($formStatuses[$statusIndex]) && isset($formStatuses[$statusIndex][$parameter]));
    }

    /**
     * Check if $parameter is requested to be turned on
     *
     * @param string  $parameter
     * @param boolean $oldValue
     * @param array   $formStatuses
     * @param string  $statusIndex
     *
     * @return boolean
     */
    protected function isParameterTurnedOff($parameter, $oldValue, array $formStatuses, $statusIndex)
    {
        return ($oldValue && (!isset($formStatuses[$statusIndex]) || !isset($formStatuses[$statusIndex][$parameter])));
    }
}
