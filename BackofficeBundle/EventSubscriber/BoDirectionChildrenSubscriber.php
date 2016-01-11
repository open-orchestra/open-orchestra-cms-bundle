<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class BoDirectionChildrenSubscriber
 */
class BoDirectionChildrenSubscriber implements EventSubscriberInterface
{
    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (null !== $data->getId()) {
            $form->add('boDirection', 'orchestra_direction', array(
                'label' => 'open_orchestra_backoffice.form.node.boDirection',
            ));
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData'
        );
    }
}
