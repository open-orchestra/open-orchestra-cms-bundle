<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * Class AbstractModulableTypeSubscriber
 */
abstract class AbstractModulableTypeSubscriber implements EventSubscriberInterface
{
    /**
     * @param FormEvent $event
     */
    abstract public function preSetData(FormEvent $event);

    /**
     * @param FormEvent $event
     */
    abstract public function preSubmit(FormEvent $event);

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * @param string        $value
     * @param FormInterface $form
     *
     * @return mixed
     */
    protected function transformData($value, FormInterface $form)
    {
        if (is_array($value) && !is_null($form->getConfig()->getOption('type'))) {
            $tmpValue = null;
            foreach ($value as $key => $element) {
                if ($form->has($key)) {
                    $tmpValue[] = $this->transformData($element, $form->get($key));
                } else {
                    $tmpValue[] = $element;
                }
            }

            return $tmpValue;

        }
        $viewTransformers = $form->getConfig()->getViewTransformers();
        $modelTransformers = $form->getConfig()->getModelTransformers();
        foreach ($viewTransformers as $viewTransformer) {
            $value = $viewTransformer->reverseTransform($value);
        }
        foreach ($modelTransformers as $modelTransformer) {
            $value = $modelTransformer->reverseTransform($value);
        }

        return $value;
    }
}
