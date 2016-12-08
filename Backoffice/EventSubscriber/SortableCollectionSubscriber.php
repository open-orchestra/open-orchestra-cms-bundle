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
            if ($data instanceof Collection) {

                foreach ($order as $key => $value) {
                    $child = $data->get($key);
                    $data->remove($key);
                    $data->set($key, $child);
                }
            }
            if (is_array($data)) {
                $data = array_merge($order, $data);
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
