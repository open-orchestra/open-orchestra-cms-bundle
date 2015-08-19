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
            $children = null;
            foreach ($value as $key => $element) {
                if ($form->has($key)) {
                    $children = $form->get($key);
                }
                if ($children instanceof FormInterface) {
                    $tmpValue[] = $this->transformData($element, $children);
                } else {
                    $tmpValue[] = $element;
                }
            }
            foreach (array_keys($form->all()) as $key) {
                if (!array_key_exists($key, $value)) {
                    $form->remove($key);
                }
            }


            return $tmpValue;

        }
        $viewTransformers = $form->getConfig()->getViewTransformers();
        $modelTransformers = $form->getConfig()->getModelTransformers();
        for ($i = count($viewTransformers) - 1; $i >= 0; --$i) {
            $value = $viewTransformers[$i]->reverseTransform($value);
        }
        for ($i = count($modelTransformers) - 1; $i >= 0; --$i) {
            $value = $modelTransformers[$i]->reverseTransform($value);
        }

        return $value;
    }
}
