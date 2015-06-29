<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

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

        $optionsField = array(
            'label' => $option['label'],
            'required' => $option['required']
        );
        if ($option['required'] == true) {
            $optionsField['constraints'] = new NotBlank();
        }
        $form->add('value', $option['type'], $optionsField);
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
