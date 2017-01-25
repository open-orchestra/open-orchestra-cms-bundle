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
                    ->trans('open_orchestra_workflow_admin.status.translation_state'      , array(), 'interface'),
                $this->get('translator')
                    ->trans('open_orchestra_workflow_admin.status.published_state'        , array(), 'interface'),
                $this->get('translator')
                    ->trans('open_orchestra_workflow_admin.status.auto_publish_from_state', array(), 'interface'),
                $this->get('translator')
                    ->trans('open_orchestra_workflow_admin.status.auto_unpublish_to_state', array(), 'interface'),
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
        $formStatuses = $formData['statuses'];
        $originalStatusesMap = $this->generateStatusesPropertiesMap($statusCollection);

        $newPropertiesIndexes = $this->getStatusIndexesWithNewProperty($originalStatusesMap, $formStatuses);
        $lostPropertiesIndexes = $this->getStatusIndexesWithLostProperty($originalStatusesMap, $formStatuses);

        return array_unique(array_merge($newPropertiesIndexes, $lostPropertiesIndexes));
    }

    /**
     * Generate a properties map of available properties by status
     *
     * @param array $statusCollection
     *
     * @return string
     */
    protected function generateStatusesPropertiesMap(array $statusCollection)
    {
        $statusMap = array();

        foreach ($statusCollection as $index => $status) {
            if ($status->isInitialState()) {
                $statusMap[$index]['initialState'] = '1';
            }
            if ($status->isTranslationState()) {
                $statusMap[$index]['translationState'] = '1';
            }
            if ($status->isPublishedState()) {
                $statusMap[$index]['publishedState'] = '1';
            }
            if ($status->isAutoPublishFromState()) {
                $statusMap[$index]['autoPublishFromState'] = '1';
            }
            if ($status->isAutoUnpublishToState()) {
                $statusMap[$index]['autoUnpublishToState'] = '1';
            }
        }

        return $statusMap;
    }

    /**
     * Get indexes of status with properties activated
     *
     * @param array $originalStatusesMap
     * @param array $formStatuses
     *
     * @return array
     */
    protected function getStatusIndexesWithNewProperty(array $originalStatusesMap, array $formStatuses)
    {
        $diff = array_udiff_assoc($formStatuses, $originalStatusesMap, 'array_diff_assoc');

        return array_keys($diff);
    }

    /**
     * Get indexes of status with properties removed
     *
     * @param array $originalStatusesMap
     * @param array $formStatuses
     *
     * @return array
     */
    protected function getStatusIndexesWithLostProperty(array $originalStatusesMap, array $formStatuses)
    {
        $diff = array_udiff_assoc($originalStatusesMap, $formStatuses, 'array_diff_assoc');

        return array_keys($diff);
    }
}
