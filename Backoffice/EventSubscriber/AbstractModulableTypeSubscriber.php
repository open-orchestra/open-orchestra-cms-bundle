<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

@trigger_error('The '.__NAMESPACE__.'\AbstractModulableTypeSubscriber class is deprecated since version 1.2.0 and will be removed in 1.3.0', E_USER_DEPRECATED);

/**
 * Class AbstractModulableTypeSubscriber
 * @deprecated  deprecated since version 1.2.0 and will be removed in version 1.3.0
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
        if (is_array($value) && (!is_null($form->getConfig()->getOption('type')) || $form->count() > 0)) {
            $children = null;
            foreach ($value as $key => $element) {
                if ($form->has($key)) {
                    $children = $form->get($key);
                }
                if ($children instanceof FormInterface) {
                    $value[$key] = $this->transformData($element, $children);
                } else {
                    $value[$key] = $element;
                }
            }
            foreach (array_keys($form->all()) as $key) {
                if (!array_key_exists($key, $value)) {
                    $form->remove($key);
                }
            }

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
