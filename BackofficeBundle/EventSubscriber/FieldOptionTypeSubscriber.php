<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class FieldOptionTypeSubscriber
 */
class FieldOptionTypeSubscriber implements EventSubscriberInterface
{
    protected $options;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $element = $event->getData();
        $option = $this->options[$element->getKey()];
        $form = $event->getForm();
        $form->add('value', $option['type'], array(
            'label' => $option['label'],
            'required' => $option['required']
        ));
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
        );
    }

}
