<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * Class AbstractBlockContentTypeSubscriber
 */
abstract class AbstractBlockContentTypeSubscriber implements EventSubscriberInterface
{
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
