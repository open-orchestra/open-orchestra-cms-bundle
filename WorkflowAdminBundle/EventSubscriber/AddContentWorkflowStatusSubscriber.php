<?php

namespace OpenOrchestra\WorkflowAdminBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\Backoffice\Event\ContentFormEvent;
use OpenOrchestra\Backoffice\ContentFormEvents;

/**
 * Class AddContentWorkflowStatusSubscriber
 *
 */
class AddContentWorkflowStatusSubscriber implements EventSubscriberInterface
{
    /**
     * add workflowStatus choice to content form
     *
     * @param NodeEvent $event
     */
    public function addWorkflowStatus(ContentFormEvent $event)
    {
        $builder = $event->getBuilder();

        $builder->add('status', 'oo_status_choice', array(
            'label' => 'open_orchestra_backoffice.form.content.status',
            'group_id' => 'property',
            'sub_group_id' => 'publication',
        ));
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ContentFormEvents::CONTENT_FORM_CREATION => 'addWorkflowStatus',
        );
    }
}
