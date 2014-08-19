<?php

namespace PHPOrchestra\BackofficeBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class BlockTypeSubscriber
 */
class BlockTypeSubscriber implements EventSubscriberInterface
{
    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        foreach ($data->getAttributes() as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $form->add('field_' . $key, 'text', array(
                'label' => $key,
                'data' => $value,
                'mapped' => false
            ));
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $block = $form->getData();
        $blockAttributes = $block->getAttributes();
        $data = $event->getData();

        foreach ($data as $key => $value) {
            if ('component' == $key || 'submit' == $key) {
                continue;
            }
            $blockAttributes[$key] = (is_array(json_decode($value, true)))?json_decode($value, true): $value;
        }

        $block->setAttributes($blockAttributes);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit'
        );
    }
}
