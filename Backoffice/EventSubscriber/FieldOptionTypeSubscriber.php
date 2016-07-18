<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

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

        if ($option['required'] === true) {
            $option['constraints'] = new NotBlank();
        }

        $formType = $option['type'];
        unset($option['type']);

        $form->add('value', $formType, $option);
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
