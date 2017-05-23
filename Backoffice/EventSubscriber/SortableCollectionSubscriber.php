<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Class SortableCollectionSubscriber
 */
class SortableCollectionSubscriber implements EventSubscriberInterface
{
    /**
     * Triggered when a collection is submitted
     *
     * @param FormEvent $event
     *
     * @return FormInterface
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $form->getData();

        if (is_array($event->getData())) {
            $order = array_flip(array_keys($event->getData()));
            if (is_null($data)) {
                $data = array();
            }

            if ($data instanceof Collection) {
                $dataClone = $data->toArray();
                $data->clear();
                foreach ($order as $key => $value) {
                    $data->set($key, array_key_exists($key, $dataClone) ? $dataClone[$key] : null);
                }
            } elseif (is_array($data)) {
                $oldData = array_merge(array(), $data);
                $data = array();
                foreach ($order as $key => $value) {
                    $data[$key] = array_key_exists($key, $oldData) ? $oldData[$key] : null;
                }

            }
            $form->setData($data);
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }
}
